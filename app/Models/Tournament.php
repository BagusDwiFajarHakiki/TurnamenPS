<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\TournamentStage;
use App\Services\TournamentService;

class Tournament extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saving(function ($tournament) {
            if (empty($tournament->slug)) {
                $tournament->slug = \Illuminate\Support\Str::slug($tournament->name);
            }
        });

        static::deleting(function ($tournament) {
            if ($tournament->qris_image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($tournament->qris_image_path);
            }

            // Hapus bukti pembayaran pendaftaran yang terkait dengan turnamen ini
            $batchProofs = \App\Models\EntryBatch::where('tournament_id', $tournament->id)
                ->whereNotNull('payment_proof_path')
                ->pluck('payment_proof_path')
                ->toArray();

            $entryProofs = \App\Models\TournamentEntry::where('tournament_id', $tournament->id)
                ->whereNotNull('payment_proof_path')
                ->pluck('payment_proof_path')
                ->toArray();

            $allProofs = array_unique(array_merge($batchProofs, $entryProofs));

            if (!empty($allProofs)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($allProofs);
            }
        });

        static::updated(function ($tournament) {
            // Jika waktu pendaftaran diundur (menjadi di masa depan) dan bagan sudah terbuat
            if ($tournament->wasChanged('registration_end') && $tournament->registration_end->isFuture()) {
                if ($tournament->stages()->exists()) {
                    // Hapus bagan yang sudah ada
                    $tournament->stages()->delete();
                    
                    // Kembalikan status ke registration
                    $tournament->updateQuietly(['status' => 'registration']);
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'price_per_slot',
        'min_slots_per_player',
        'max_slot_per_player',
        'max_entries',
        'entry_expiry_hours',
        'payment_info',
        'qris_image_path',
        'rules_content',
        'no_show_deadline_minutes',
        'registration_start',
        'registration_end',
        'tournament_start',
        'tournament_end',
        'status',
    ];

    protected $casts = [
        'price_per_slot' => 'decimal:2',
        'min_slots_per_player' => 'integer',
        'max_slot_per_player' => 'integer',
        'max_entries' => 'integer',
        'entry_expiry_hours' => 'integer',
        'no_show_deadline_minutes' => 'integer',
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'tournament_start' => 'datetime',
        'tournament_end' => 'datetime',
    ];

    public function stages(): HasMany
    {
        return $this->hasMany(TournamentStage::class)->orderBy('stage_order');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TournamentEntry::class);
    }

    public function entryBatches(): HasMany
    {
        return $this->hasMany(EntryBatch::class);
    }

    public function aggregates(): HasMany
    {
        return $this->hasMany(TournamentPlayerAggregate::class);
    }

    public function getJuara(int $position): ?string
    {
        $stageIds = $this->stages()->pluck('id')->toArray();
        if (empty($stageIds)) {
            return null;
        }

        if ($position === 3) {
            $match = GameMatch::whereIn('tournament_stage_id', $stageIds)
                ->where('bracket_position', '3rd_place')
                ->whereIn('status', ['completed', 'walkover'])
                ->with('participants.entry.player')
                ->first();

            if ($match) {
                $winner = $match->participants->firstWhere('is_winner', true);
                return $winner?->entry?->player?->name;
            }
            return null;
        }

        if ($position === 1 || $position === 2) {
            // Final match is the highest round match that is not 3rd_place
            $match = GameMatch::whereIn('tournament_stage_id', $stageIds)
                ->where('bracket_position', '!=', '3rd_place')
                ->whereIn('status', ['completed', 'walkover'])
                ->orderByDesc('round_number')
                ->with('participants.entry.player')
                ->first();

            if ($match) {
                $targetParticipant = $position === 1 
                    ? $match->participants->firstWhere('is_winner', true)
                    : $match->participants->firstWhere('is_winner', false);
                    
                return $targetParticipant?->entry?->player?->name;
            }
            return null;
        }

        return null;
    }

    public function getTopScorers(int $limit = 5)
    {
        return $this->aggregates()->with('player')->where('total_goals_scored', '>', 0)->orderByDesc('total_goals_scored')->limit($limit)->get();
    }


    /**
     * Try to auto-generate bracket when registration ends.
     * Checks conditions and generates bracket if ready.
     * Called from check-in, payment verification, or page loads.
     */
    public function tryAutoGenerateBracket(): bool
    {
        $now = now();

        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return false;
        }

        if ($this->stages()->exists()) {
            return false;
        }

        if ($this->tournament_end <= $now) {
            return false;
        }

        if ($this->registration_end > $now) {
            return false;
        }

        $verifiedCount = $this->entries()->where('status', 'verified')->count();

        if ($verifiedCount < 2) {
            return false;
        }

        DB::transaction(function () {
            $stage = TournamentStage::create([
                'tournament_id' => $this->id,
                'name' => 'Sistem Gugur',
                'stage_order' => 1,
                'format' => 'single_elimination',
                'source_type' => 'registration',
                'status' => 'pending',
            ]);

            app(TournamentService::class)->generateBracket($stage);

            $this->update(['status' => 'ongoing']);

            $verifiedEntries = $this->entries()->where('status', 'verified')->get();
            
            // Kelompokkan berdasarkan peserta agar pembagian slot yang kosong (TBD) adil
            $groupedByPlayer = $verifiedEntries->groupBy('player_id');
            $interleavedEntries = collect();
            
            $maxSlots = $groupedByPlayer->max(fn($group) => $group->count());
            
            for ($i = 0; $i < $maxSlots; $i++) {
                foreach ($groupedByPlayer as $playerEntries) {
                    if ($playerEntries->has($i)) {
                        $interleavedEntries->push($playerEntries[$i]);
                    }
                }
            }

            $service = app(TournamentService::class);
            foreach ($interleavedEntries as $entry) {
                $service->assignSlot($entry);
            }
        });

        return true;
    }

    /**
     * When tournament_start arrives, auto-BYE single-player matches
     * and set two-player matches to ready.
     */
    public function tryStartTournament(): bool
    {
        $now = now();

        if ($this->status !== 'ongoing') {
            return false;
        }

        if ($this->tournament_start > $now) {
            return false;
        }

        $hasUnscheduled = $this->stages()->where('status', 'ongoing')
            ->whereHas('matches', function ($q) {
                $q->whereIn('status', ['pending'])
                  ->orWhere(function ($q2) {
                      $q2->where('round_number', 1)
                         ->where('status', 'ready')
                         ->whereHas('participants', function ($pq) {
                             $pq->whereNull('tournament_entry_id');
                         });
                  });
            })->exists();

        if (!$hasUnscheduled) {
            return false;
        }

        app(TournamentService::class)->startTournament($this);

        return true;
    }
}
