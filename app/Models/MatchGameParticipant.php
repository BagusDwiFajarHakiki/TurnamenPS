<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchGameParticipant extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'match_game_id',
        'tournament_entry_id',
        'club_id',
        'goals_scored',
        'is_winner',
    ];

    protected $casts = [
        'goals_scored' => 'integer',
        'is_winner' => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_game_id');
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
