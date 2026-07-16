<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerLoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'username_attempted',
        'ip_address',
        'success',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
