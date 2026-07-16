<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Services\TournamentService;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'tournament_stage_id',
        'group_id',
        'round_number',
        'match_order',
        'bracket_position',
        'next_match_id',
        'loser_next_match_id',
        'is_bye',
        'status',
        'ps_unit_id',
        'scheduled_at',
        'started_at',
        'finished_at',
        'best_of',
        'decided_by_penalty',
        'penalty_score_home',
        'penalty_score_away',
        'result_proof_path',
        'no_show_entry_id',
        'walkover_reason',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'match_order' => 'integer',
        'is_bye' => 'boolean',
        'best_of' => 'integer',
        'decided_by_penalty' => 'boolean',
        'penalty_score_home' => 'integer',
        'penalty_score_away' => 'integer',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(TournamentStage::class, 'tournament_stage_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }

    public function psUnit(): BelongsTo
    {
        return $this->belongsTo(PsUnit::class, 'ps_unit_id');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'next_match_id');
    }

    public function loserNextMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'loser_next_match_id');
    }

    public function noShowEntry(): BelongsTo
    {
        return $this->belongsTo(TournamentEntry::class, 'no_show_entry_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(MatchParticipant::class, 'match_id');
    }

    public function games(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'match_id');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(MatchDispute::class, 'match_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PsUnitSchedule::class, 'match_id');
    }

    public function getHomeParticipantAttribute()
    {
        return $this->participants->where('side', 'home')->first();
    }

    public function getAwayParticipantAttribute()
    {
        return $this->participants->where('side', 'away')->first();
    }

    /**
     * Resolve match result and advance winner to next round.
     * Called EXPLICITLY only — never via model events.
     */
    public function resolveResultAndAdvance(): void
    {
        $this->load(['participants', 'participants.entry', 'stage']);

        if ($this->status === 'walkover') {
            $this->resolveWalkover();
        } elseif ($this->status === 'completed') {
            $this->resolveCompleted();
        } else {
            $this->revokeAdvancement();
        }
    }

    private function resolveWalkover(): void
    {
        $noShowEntryId = $this->no_show_entry_id;
        if (!$noShowEntryId) return;

        DB::transaction(function () use ($noShowEntryId) {
            $home = $this->participants->where('side', 'home')->first();
            $away = $this->participants->where('side', 'away')->first();
            if (!$home || !$away) return;

            $homeIsNoShow = $home->tournament_entry_id == $noShowEntryId;

            $home->updateQuietly([
                'goals_scored' => $homeIsNoShow ? 0 : 3,
                'is_winner' => !$homeIsNoShow,
            ]);
            $away->updateQuietly([
                'goals_scored' => $homeIsNoShow ? 3 : 0,
                'is_winner' => $homeIsNoShow,
            ]);

            $winnerEntry = $homeIsNoShow ? $away->entry : $home->entry;
            if ($winnerEntry) {
                app(TournamentService::class)->advanceWinner($this, $winnerEntry);
            }

            $noShowEntry = TournamentEntry::find($noShowEntryId);
            if ($noShowEntry) {
                $noShowEntry->increment('walkover_count');
                if ($noShowEntry->walkover_count >= 2) {
                    $noShowEntry->update(['status' => 'disqualified']);
                }
            }

            $this->updateQuietly(['finished_at' => now()]);
            $this->releasePsUnit();
        });
    }

    private function resolveCompleted(): void
    {
        DB::transaction(function () {
            $home = $this->participants->where('side', 'home')->first();
            $away = $this->participants->where('side', 'away')->first();
            if (!$home || !$away) return;

            $homeWinner = false;
            $awayWinner = false;

            if ($this->decided_by_penalty) {
                $homeWinner = $this->penalty_score_home > $this->penalty_score_away;
                $awayWinner = $this->penalty_score_away > $this->penalty_score_home;
            } else {
                $homeWinner = $home->goals_scored > $away->goals_scored;
                $awayWinner = $away->goals_scored > $home->goals_scored;
            }

            $home->updateQuietly(['is_winner' => $homeWinner]);
            $away->updateQuietly(['is_winner' => $awayWinner]);

            $winnerEntry = $homeWinner ? $home->entry : ($awayWinner ? $away->entry : null);
            if ($winnerEntry) {
                app(TournamentService::class)->advanceWinner($this, $winnerEntry);
            }

            $this->updateQuietly(['finished_at' => now()]);
            $this->releasePsUnit();
        });
    }

    public function revokeAdvancement(): void
    {
        DB::transaction(function () {
            foreach ($this->participants as $part) {
                if ($part->is_winner) {
                    $part->updateQuietly(['is_winner' => false]);
                }

                if ($this->next_match_id && $part->tournament_entry_id) {
                    $nextParticipant = MatchParticipant::where('match_id', $this->next_match_id)
                        ->where('tournament_entry_id', $part->tournament_entry_id)
                        ->first();

                    if ($nextParticipant) {
                        $nextParticipant->updateQuietly([
                            'tournament_entry_id' => null,
                            'club_id' => null,
                        ]);
                    }
                }
            }

            if ($this->next_match_id) {
                $nextMatch = GameMatch::find($this->next_match_id);
                if ($nextMatch) {
                    $filled = MatchParticipant::where('match_id', $nextMatch->id)
                        ->whereNotNull('tournament_entry_id')
                        ->count();

                    if ($nextMatch->status === 'completed' && $nextMatch->is_bye && $filled < 2) {
                        $nextMatch->updateQuietly([
                            'status' => 'pending',
                            'is_bye' => false,
                            'finished_at' => null,
                        ]);
                        $nextMatch->revokeAdvancement();
                    } elseif (in_array($nextMatch->status, ['ready', 'scheduled'])) {
                        $nextMatch->updateQuietly(['status' => $filled >= 2 ? 'ready' : 'pending']);
                    }
                }
            }

            $this->updateQuietly(['finished_at' => null]);
        });
    }

    private function releasePsUnit(): void
    {
        if (!$this->ps_unit_id) return;

        PsUnitSchedule::where('match_id', $this->id)
            ->where('status', 'booked')
            ->update([
                'booked_until' => now(),
                'status' => 'completed',
            ]);

        app(TournamentService::class)->processQueue();
    }
}
