<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'raised_by_entry_id',
        'reason',
        'status',
        'reviewed_by',
        'resolution_note',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($dispute) {
            if ($dispute->isDirty('status') && in_array($dispute->status, ['upheld', 'rejected'])) {
                $dispute->resolved_at = now();
                $dispute->reviewed_by = auth()->id();
            }
        });
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(TournamentEntry::class, 'raised_by_entry_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
