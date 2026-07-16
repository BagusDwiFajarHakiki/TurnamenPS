<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PsUnitSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'ps_unit_id',
        'match_id',
        'booked_from',
        'booked_until',
        'status',
    ];

    protected $casts = [
        'booked_from' => 'datetime',
        'booked_until' => 'datetime',
    ];

    public function psUnit(): BelongsTo
    {
        return $this->belongsTo(PsUnit::class, 'ps_unit_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }
}
