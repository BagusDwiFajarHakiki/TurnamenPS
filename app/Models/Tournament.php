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
        'rules_content',
        'no_show_deadline_minutes',
        'check_in_open_minutes_before',
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
        'check_in_open_minutes_before' => 'integer',
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
            $service = app(TournamentService::class);
            foreach ($verifiedEntries as $entry) {
                $service->fillSlotOnCheckIn($entry);
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
                $q->whereIn('status', ['pending']);
            })->exists();

        if (!$hasUnscheduled) {
            return false;
        }

        app(TournamentService::class)->startTournament($this);

        return true;
    }
}
