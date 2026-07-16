<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TournamentPlayerAggregate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'player_id',
        'total_entries',
        'total_matches_played',
        'total_goals_scored',
        'total_goals_conceded',
        'total_wins',
        'total_losses',
        'total_draws',
        'current_win_streak',
        'best_win_streak',
        'active_entries_count',
        'rank_position',
    ];

    protected $casts = [
        'total_entries' => 'integer',
        'total_matches_played' => 'integer',
        'total_goals_scored' => 'integer',
        'total_goals_conceded' => 'integer',
        'total_wins' => 'integer',
        'total_losses' => 'integer',
        'total_draws' => 'integer',
        'current_win_streak' => 'integer',
        'best_win_streak' => 'integer',
        'active_entries_count' => 'integer',
        'rank_position' => 'integer',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
