<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TournamentEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'player_id',
        'entry_batch_id',
        'group_id',
        'club_id',
        'entry_label',
        'entry_number',
        'seed',
        'status',
        'payment_proof_path',
        'payment_verified_at',
        'payment_verified_by',
        'expires_at',
        'walkover_count',
        'rules_accepted_at',
        'registered_at',
    ];

    protected $casts = [
        'entry_number' => 'integer',
        'seed' => 'integer',
        'walkover_count' => 'integer',
        'payment_verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'rules_accepted_at' => 'datetime',
        'registered_at' => 'datetime',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(EntryBatch::class, 'entry_batch_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function matchParticipants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class, 'tournament_entry_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(MatchDispute::class, 'raised_by_entry_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->player?->name ?? 'BYE';
        return $name . ' ' . ($this->entry_number ?? '');
    }
}
