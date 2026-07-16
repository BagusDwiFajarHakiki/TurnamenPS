<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'game_number',
    ];

    protected $casts = [
        'game_number' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(MatchGameParticipant::class, 'match_game_id');
    }
}
