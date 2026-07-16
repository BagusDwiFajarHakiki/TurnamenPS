<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TournamentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_stage_id',
        'name',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(TournamentStage::class, 'tournament_stage_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TournamentEntry::class, 'group_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'group_id');
    }
}
