<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerCodeResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'username_submitted',
        'status',
        'new_code_issued_by',
        'issued_same_code',
        'resolved_at',
    ];

    protected $casts = [
        'issued_same_code' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_code_issued_by');
    }
}
