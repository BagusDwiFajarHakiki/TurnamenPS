<?php

namespace App\Events;

use App\Models\GameMatch;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchCompleted
{
    use Dispatchable, SerializesModels;

    public GameMatch $match;

    /**
     * Create a new event instance.
     */
    public function __construct(GameMatch $match)
    {
        $this->match = $match;
    }
}
