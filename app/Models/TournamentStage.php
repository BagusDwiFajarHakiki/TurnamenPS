<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TournamentStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'name',
        'stage_order',
        'format',
        'status',
        'source_type',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'stage_order' => 'integer',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(TournamentGroup::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'tournament_stage_id');
    }
}
