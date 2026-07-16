<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchParticipant extends Model
{
    use HasFactory;

    protected $table = 'match_participants';

    protected $fillable = [
        'match_id',
        'tournament_entry_id',
        'side',
        'club_id',
        'goals_scored',
        'is_winner',
    ];

    protected $casts = [
        'goals_scored' => 'integer',
        'is_winner' => 'boolean',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(TournamentEntry::class, 'tournament_entry_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
