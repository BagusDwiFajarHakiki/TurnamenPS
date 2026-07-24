<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'player_id',
        'slot_count',
        'total_price',
        'payment_proof_path',
        'payment_method',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'slot_count' => 'integer',
        'total_price' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($batch) {
            if ($batch->isDirty('status') && $batch->status === 'verified') {
                $batch->loadMissing('tournament');
                
                // Cek batas slot per peserta
                $existingCount = \App\Models\TournamentEntry::where('tournament_id', $batch->tournament_id)
                    ->where('player_id', $batch->player_id)
                    ->count();

                if ($existingCount + $batch->slot_count > $batch->tournament->max_slot_per_player) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'status' => 'Total slot peserta ini melebihi batas maksimal (' . $batch->tournament->max_slot_per_player . ' slot per peserta).'
                    ]);
                }

                // Cek batas total slot turnamen
                $totalVerified = \App\Models\TournamentEntry::where('tournament_id', $batch->tournament_id)->count();
                if ($totalVerified + $batch->slot_count > $batch->tournament->max_entries) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'status' => 'Total slot melebihi sisa kuota turnamen yang tersedia.'
                    ]);
                }
            }
        });

        static::updated(function ($batch) {
            if ($batch->wasChanged('status')) {
                if ($batch->status === 'verified') {
                    $batch->loadMissing('player');
                    
                    $existingCount = \App\Models\TournamentEntry::where('tournament_id', $batch->tournament_id)
                        ->where('player_id', $batch->player_id)
                        ->count();

                    for ($i = 1; $i <= $batch->slot_count; $i++) {
                        $slotNum = $existingCount + $i;
                        $entry = \App\Models\TournamentEntry::create([
                            'tournament_id' => $batch->tournament_id,
                            'player_id' => $batch->player_id,
                            'entry_batch_id' => $batch->id,
                            'entry_label' => $batch->player->name . " {$slotNum}",
                            'entry_number' => $slotNum,
                            'status' => 'verified',
                            'payment_proof_path' => $batch->payment_proof_path,
                            'payment_verified_at' => now(),
                            'payment_verified_by' => auth()->id(),
                            'registered_at' => now(),
                        ]);

                        app(\App\Services\TournamentService::class)->assignSlot($entry);
                    }
                    
                    $batch->updateQuietly([
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                    ]);

                    $tournament = \App\Models\Tournament::find($batch->tournament_id);
                    if ($tournament) {
                        $tournament->tryAutoGenerateBracket();
                    }
                } elseif ($batch->status === 'rejected') {
                    // Tarik slot aktif yang sebelumnya disetujui (jika ada)
                    $entries = \App\Models\TournamentEntry::where('entry_batch_id', $batch->id)->get();
                    
                    foreach ($entries as $entry) {
                        // Kosongkan slot di bagan turnamen sebelum menghapus entry agar slot tidak terhapus permanen oleh cascadeOnDelete
                        $entry->matchParticipants()->update([
                            'tournament_entry_id' => null,
                            'club_id' => null,
                            'goals_scored' => 0,
                            'is_winner' => null
                        ]);
                        $entry->delete();
                    }
                }
            }
        });

        static::deleted(function ($batch) {
            if ($batch->payment_proof_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($batch->payment_proof_path);
            }
        });
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TournamentEntry::class, 'entry_batch_id');
    }
}
