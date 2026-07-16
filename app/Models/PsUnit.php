<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PsUnit extends Model
{
    use HasFactory;

    protected $table = 'ps_units';

    protected $fillable = [
        'code',
        'name',
        'location',
        'console_type',
        'controller_count',
        'status',
        'notes',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'ps_unit_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PsUnitSchedule::class, 'ps_unit_id');
    }
}
