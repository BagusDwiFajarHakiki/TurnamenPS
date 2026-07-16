<?php

namespace App\Listeners;

use App\Events\MatchCompleted;
use App\Models\MatchParticipant;
use App\Models\TournamentEntry;
use App\Models\TournamentPlayerAggregate;
use Illuminate\Support\Facades\DB;

class UpdatePlayerAggregate
{
    /**
     * Handle the event.
     */
    public function handle(MatchCompleted $event): void
    {
        $match = $event->match;
        $tournamentId = $match->stage->tournament_id;

        // Find all player IDs involved in this match
        $entryIds = MatchParticipant::where('match_id', $match->id)
            ->pluck('tournament_entry_id')
            ->toArray();

        $players = TournamentEntry::whereIn('id', $entryIds)
            ->pluck('player_id')
            ->unique();

        foreach ($players as $playerId) {
            DB::transaction(function () use ($tournamentId, $playerId) {
                // Fetch all entries of this player in the current tournament
                $playerEntries = TournamentEntry::where('tournament_id', $tournamentId)
                    ->where('player_id', $playerId)
                    ->get();

                $playerEntryIds = $playerEntries->pluck('id')->toArray();

                // 1. Calculate active entries count (checked_in, active, verified)
                $activeEntriesCount = $playerEntries->whereIn('status', ['verified', 'checked_in', 'active'])->count();

                // 2. Fetch match history for all entries of this player in this tournament
                $playedParticipants = MatchParticipant::whereIn('tournament_entry_id', $playerEntryIds)
                    ->whereHas('match', function ($query) {
                        $query->whereIn('status', ['completed', 'walkover']);
                    })
                    ->get();

                $totalMatchesPlayed = $playedParticipants->count();
                $totalGoalsScored = $playedParticipants->sum('goals_scored');

                // Calculate goals conceded, wins, losses, draws
                $totalGoalsConceded = 0;
                $wins = 0;
                $losses = 0;
                $draws = 0;

                foreach ($playedParticipants as $pPart) {
                    // Find opponent in the same match
                    $opponentPart = MatchParticipant::where('match_id', $pPart->match_id)
                        ->where('id', '!=', $pPart->id)
                        ->first();

                    if ($opponentPart) {
                        $totalGoalsConceded += $opponentPart->goals_scored;

                        if ($pPart->is_winner === true) {
                            $wins++;
                        } elseif ($pPart->is_winner === false) {
                            if ($opponentPart->is_winner === true) {
                                $losses++;
                            } else {
                                $draws++;
                            }
                        }
                    } else {
                        // Single-sided/Walkover matches
                        if ($pPart->is_winner === true) {
                            $wins++;
                        } else {
                            $losses++;
                        }
                    }
                }

                // 3. Calculate Win Streak chronologically across all slots/entries
                $history = MatchParticipant::whereIn('tournament_entry_id', $playerEntryIds)
                    ->join('matches', 'match_participants.match_id', '=', 'matches.id')
                    ->whereIn('matches.status', ['completed', 'walkover'])
                    ->orderBy('matches.finished_at')
                    ->select('match_participants.*', 'matches.finished_at')
                    ->get();

                $currentStreak = 0;
                $bestStreak = 0;

                foreach ($history as $hPart) {
                    if ($hPart->is_winner === true) {
                        $currentStreak++;
                        if ($currentStreak > $bestStreak) {
                            $bestStreak = $currentStreak;
                        }
                    } else {
                        $currentStreak = 0; // Reset streak on loss/draw
                    }
                }

                // 4. Save or update aggregate row
                TournamentPlayerAggregate::updateOrCreate(
                    [
                        'tournament_id' => $tournamentId,
                        'player_id' => $playerId,
                    ],
                    [
                        'total_entries' => $playerEntries->count(),
                        'total_matches_played' => $totalMatchesPlayed,
                        'total_goals_scored' => $totalGoalsScored,
                        'total_goals_conceded' => $totalGoalsConceded,
                        'total_wins' => $wins,
                        'total_losses' => $losses,
                        'total_draws' => $draws,
                        'current_win_streak' => $currentStreak,
                        'best_win_streak' => $bestStreak,
                        'active_entries_count' => $activeEntriesCount,
                    ]
                );
            });
        }

        // 5. Recompute leaderboard positions for all players in this tournament
        $allAggregates = TournamentPlayerAggregate::where('tournament_id', $tournamentId)
            ->orderByDesc('total_goals_scored')
            ->orderByDesc('total_wins')
            ->orderByDesc('best_win_streak')
            ->get();

        foreach ($allAggregates as $index => $aggregate) {
            $aggregate->update(['rank_position' => $index + 1]);
        }
    }
}
