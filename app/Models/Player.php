<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Player extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'login_code',
        'login_code_plain_hint',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'login_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($player) {
            if (empty($player->login_code)) {
                $plain = $player->login_code_plain_hint ?: (\Illuminate\Support\Str::random(10) . '1aA');
                $player->login_code = bcrypt($plain);
                $player->login_code_plain_hint = $plain;
            } else {
                if (!str_starts_with($player->login_code, '$2y$')) {
                    $player->login_code_plain_hint = $player->login_code;
                    $player->login_code = bcrypt($player->login_code);
                }
            }
        });

        static::updating(function ($player) {
            if ($player->isDirty('login_code') && !str_starts_with($player->login_code, '$2y$')) {
                $player->login_code_plain_hint = $player->login_code;
                $player->login_code = bcrypt($player->login_code);
            }
        });
    }

    // Define auth password field (uses login_code instead of default password)
    public function getAuthPasswordName(): string
    {
        return 'login_code';
    }

    public function getAuthPassword(): string
    {
        return $this->login_code;
    }

    public function tournamentEntries(): HasMany
    {
        return $this->hasMany(TournamentEntry::class);
    }

    public function entryBatches(): HasMany
    {
        return $this->hasMany(EntryBatch::class);
    }

    public function aggregates(): HasMany
    {
        return $this->hasMany(TournamentPlayerAggregate::class);
    }

    public function loginAttempts(): HasMany
    {
        return $this->hasMany(PlayerLoginAttempt::class);
    }

    public function codeResetRequests(): HasMany
    {
        return $this->hasMany(PlayerCodeResetRequest::class);
    }
}
