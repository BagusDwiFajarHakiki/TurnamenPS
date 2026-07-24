<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\GameMatch;
use App\Models\TournamentStage;
use App\Models\TournamentEntry;
use App\Models\MatchParticipant;
use App\Models\TournamentPlayerAggregate;
use App\Models\PsUnit;
use App\Models\PsUnitSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TournamentService
{
    public function getPlayerPerformanceScore(Player $player, $excludeTournamentId = null): float
    {
        $query = TournamentPlayerAggregate::where('player_id', $player->id);
        if ($excludeTournamentId) {
            $query->where('tournament_id', '!=', $excludeTournamentId);
        }

        $aggregates = $query->get();
        $totalWins = $aggregates->sum('total_wins');
        $totalMatches = $aggregates->sum('total_matches_played');
        $totalGoals = $aggregates->sum('total_goals_scored');
        $championships = TournamentEntry::where('player_id', $player->id)
            ->where('status', 'champion')
            ->count();
        $winRate = $totalMatches > 0 ? ($totalWins / $totalMatches) : 0;

        return ($championships * 1000) + ($totalWins * 10) + ($winRate * 5) + ($totalGoals * 0.1);
    }

    /**
     * Generate empty bracket structure based on verified entries.
     * Only creates matches and empty Round 1 participants.
     */
    public function generateBracket(TournamentStage $stage): void
    {
        DB::transaction(function () use ($stage) {
            $verifiedCount = TournamentEntry::where('tournament_id', $stage->tournament_id)
                ->where('status', 'verified')
                ->count();

            if ($verifiedCount < 2) {
                throw new \Exception('Need at least 2 verified entries.');
            }

            $bracketSize = 2;
            while ($bracketSize < $verifiedCount) {
                $bracketSize *= 2;
            }

            $numRounds = (int) log($bracketSize, 2);
            $matchesPerRound = [];

            // Create all matches for all rounds
            for ($r = 1; $r <= $numRounds; $r++) {
                $count = $bracketSize / pow(2, $r);
                $matchesPerRound[$r] = [];
                for ($m = 1; $m <= $count; $m++) {
                    $matchesPerRound[$r][$m] = GameMatch::create([
                        'tournament_stage_id' => $stage->id,
                        'round_number' => $r,
                        'match_order' => $m,
                        'bracket_position' => "{$r}.{$m}",
                        'status' => $r === 1 ? 'ready' : 'pending',
                    ]);
                }
            }

            // Link each match to its next_match_id (winner goes here)
            for ($r = 1; $r < $numRounds; $r++) {
                foreach ($matchesPerRound[$r] as $m => $match) {
                    $nextIndex = (int) ceil($m / 2);
                    $match->updateQuietly(['next_match_id' => $matchesPerRound[$r + 1][$nextIndex]->id]);
                }
            }

            // Create 3rd place match + link semifinal losers
            if ($numRounds >= 2) {
                $thirdPlace = GameMatch::create([
                    'tournament_stage_id' => $stage->id,
                    'round_number' => $numRounds,
                    'match_order' => 0,
                    'bracket_position' => '3rd_place',
                    'status' => 'ready',
                ]);

                foreach ($matchesPerRound[$numRounds - 1] as $sfMatch) {
                    $sfMatch->updateQuietly(['loser_next_match_id' => $thirdPlace->id]);
                }
            }

            // Create empty participants for Round 1 only
            foreach ($matchesPerRound[1] as $match) {
                MatchParticipant::create([
                    'match_id' => $match->id,
                    'tournament_entry_id' => null,
                    'side' => 'home',
                    'goals_scored' => 0,
                ]);
                MatchParticipant::create([
                    'match_id' => $match->id,
                    'tournament_entry_id' => null,
                    'side' => 'away',
                    'goals_scored' => 0,
                ]);
            }

            $stage->updateQuietly(['status' => 'ongoing']);
        });
    }

    /**
     * Fill a checked-in player's entry into a random empty bracket slot.
     * Uses 4-quadrant spread to ensure same player's slots are in different quadrants.
     * Ensures same player cannot appear twice in the same match card.
     */
    public function assignSlot(TournamentEntry $entry): void
    {
        $stage = TournamentStage::where('tournament_id', $entry->tournament_id)
            ->where('status', 'ongoing')
            ->first();
        if (!$stage) return;

        $allRound1Matches = GameMatch::where('tournament_stage_id', $stage->id)
            ->where('round_number', 1)
            ->orderBy('match_order')
            ->get();

        $bracketSize = $allRound1Matches->count() * 2;
        if ($bracketSize < 2) return;

        // Load all Round 1 participants
        $allParts = MatchParticipant::whereHas('match', function ($q) use ($stage) {
            $q->where('tournament_stage_id', $stage->id)->where('round_number', 1);
        })->get();

        $emptySlots = $allParts->whereNull('tournament_entry_id');
        if ($emptySlots->isEmpty()) return;

        // Load entry relation to check player_id
        $allParts->loadMissing('entry');

        // Find existing matches for this player (same player, different slots)
        $existingMatchIds = $allParts
            ->filter(function ($p) use ($entry) {
                return $p->tournament_entry_id && 
                       $p->entry &&
                       $p->entry->player_id === $entry->player_id;
            })
            ->pluck('match_id')
            ->toArray();

        // Find all player_ids that this player is already facing in Round 1
        $facedOpponentIds = [];
        foreach ($existingMatchIds as $mid) {
            $opponents = $allParts->where('match_id', $mid)
                                  ->whereNotNull('tournament_entry_id')
                                  ->filter(function($p) use ($entry) {
                                      return $p->entry && $p->entry->player_id !== $entry->player_id;
                                  });
            foreach ($opponents as $opp) {
                $facedOpponentIds[] = $opp->entry->player_id;
            }
        }
        $facedOpponentIds = array_unique($facedOpponentIds);

        // Split into empty matches (0 filled) and half-filled (1 filled)
        $emptyMatches = collect();
        $halfMatches = collect();

        foreach ($allRound1Matches as $match) {
            $filled = $allParts->where('match_id', $match->id)
                ->whereNotNull('tournament_entry_id')
                ->count();

            if ($filled === 0) {
                $emptyMatches->push($match);
            } elseif ($filled === 1) {
                $halfMatches->push($match);
            }
        }

        // Priority: empty matches without same player first, then half-filled without same player
        $safeEmpty = $emptyMatches->reject(fn($m) => in_array($m->id, $existingMatchIds));
        $safeHalf = $halfMatches->reject(fn($m) => in_array($m->id, $existingMatchIds));

        // Further filter safeHalf to avoid playing the same opponent again
        $superSafeHalf = $safeHalf->reject(function($m) use ($allParts, $facedOpponentIds) {
            $occupant = $allParts->where('match_id', $m->id)->whereNotNull('tournament_entry_id')->first();
            if ($occupant && $occupant->entry) {
                return in_array($occupant->entry->player_id, $facedOpponentIds);
            }
            return false;
        });

        $totalMatches = $allRound1Matches->count();

        // 1. Group all available matches by quadrant and type
        $quads = [0 => [], 1 => [], 2 => [], 3 => []];

        $addToQuad = function($match, $type) use (&$quads, $totalMatches) {
            $qi = (int) floor( (($match->match_order - 1) / max(1, $totalMatches)) * 4 );
            $qi = min($qi, 3);
            if (!isset($quads[$qi][$type])) $quads[$qi][$type] = collect();
            $quads[$qi][$type]->push($match);
        };

        foreach ($safeEmpty as $m) $addToQuad($m, 'safeEmpty');
        foreach ($superSafeHalf as $m) $addToQuad($m, 'superSafeHalf');
        foreach ($safeHalf as $m) $addToQuad($m, 'safeHalf');
        foreach ($emptyMatches as $m) $addToQuad($m, 'emptyMatches');
        foreach ($halfMatches as $m) $addToQuad($m, 'halfMatches');

        // 2. Determine which quadrants the player already occupies
        $usedQuads = [];
        foreach ($existingMatchIds as $mid) {
            $m = $allRound1Matches->firstWhere('id', $mid);
            if ($m) {
                $qi = (int) floor( (($m->match_order - 1) / max(1, $totalMatches)) * 4 );
                $usedQuads[] = min($qi, 3);
            }
        }
        $usedQuads = array_unique($usedQuads);
        $existingCount = count($existingMatchIds);

        // 3. Score each quadrant. Lower score is better.
        $quadScores = [];
        for ($qi = 0; $qi < 4; $qi++) {
            if (empty($quads[$qi])) continue; // No candidates here

            $score = 0;

            // Penalty for already having a slot in this exact quadrant
            if (in_array($qi, $usedQuads)) $score += 1000;

            // Penalty for violating Left/Right split if exactly 1 slot exists
            if ($existingCount === 1) {
                $existingQuad = array_values($usedQuads)[0];
                $isExistingLeft = in_array($existingQuad, [0, 1]);
                $isCandidateLeft = in_array($qi, [0, 1]);
                
                if ($isExistingLeft === $isCandidateLeft) {
                    $score += 500; // Strong penalty for being on the same side
                }
            }

            // Quality of the best match available in this quadrant
            if (isset($quads[$qi]['safeEmpty']) && $quads[$qi]['safeEmpty']->isNotEmpty()) {
                $score += 1; // Best (Empty match)
            } elseif (isset($quads[$qi]['superSafeHalf']) && $quads[$qi]['superSafeHalf']->isNotEmpty()) {
                $score += 2; // (Half match without facing someone they already face)
            } elseif (isset($quads[$qi]['safeHalf']) && $quads[$qi]['safeHalf']->isNotEmpty()) {
                $score += 3; // (Half match)
            } elseif (isset($quads[$qi]['emptyMatches']) && $quads[$qi]['emptyMatches']->isNotEmpty()) {
                $score += 4; // Fallback
            } else {
                $score += 5; // Worst Fallback
            }

            $quadScores[$qi] = $score;
        }

        if (empty($quadScores)) return;

        // 4. Find the best quadrant(s)
        $minScore = min($quadScores);
        $bestQuads = array_keys(array_filter($quadScores, fn($s) => $s === $minScore));
        
        $chosenQuadIndex = $bestQuads[array_rand($bestQuads)];

        // 5. Pick the best match from the chosen quadrant
        $matchTypes = ['safeEmpty', 'superSafeHalf', 'safeHalf', 'emptyMatches', 'halfMatches'];
        $targetMatch = null;
        foreach ($matchTypes as $type) {
            if (isset($quads[$chosenQuadIndex][$type]) && $quads[$chosenQuadIndex][$type]->isNotEmpty()) {
                $targetMatch = $quads[$chosenQuadIndex][$type]->random();
                break;
            }
        }

        if (!$targetMatch) return;

        // Find empty slot in the target match
        $emptySlot = MatchParticipant::where('match_id', $targetMatch->id)
            ->whereNull('tournament_entry_id')
            ->first();

        if (!$emptySlot) return;

        $emptySlot->updateQuietly([
            'tournament_entry_id' => $entry->id,
            'club_id' => null,
        ]);

        // If match now has 2 players, we used to set to ready automatically.
        // As per user request, this is now fully manual by admin.
        // (Removed auto-update to 'ready')
    }

    /**
     * Advance winner to next match, advance loser to 3rd-place match if applicable.
     */
    public function advanceWinner(GameMatch $completedMatch, TournamentEntry $winnerEntry): void
    {
        if ($completedMatch->bracket_position === '3rd_place') {
            $winnerEntry->update(['status' => 'champion']);
            return;
        }

        // Advance loser to 3rd place match (semifinal only)
        if ($completedMatch->loser_next_match_id) {
            $loserParticipant = MatchParticipant::where('match_id', $completedMatch->id)
                ->where('tournament_entry_id', '!=', $winnerEntry->id)
                ->first();
            if ($loserParticipant && $loserParticipant->entry) {
                $side = ($completedMatch->match_order % 2 !== 0) ? 'home' : 'away';
                $this->placeInMatch($completedMatch->loser_next_match_id, $loserParticipant->entry, $side);
            }
        }

        // No next match = this is the final
        if (!$completedMatch->next_match_id) {
            $winnerEntry->update(['status' => 'champion']);
            return;
        }

        $side = ($completedMatch->match_order % 2 !== 0) ? 'home' : 'away';
        $this->placeInMatch($completedMatch->next_match_id, $winnerEntry, $side);
    }

    /**
     * Place a participant into a specific match slot based on path (home/away).
     * Uses updateQuietly to avoid event chains.
     */
    private function placeInMatch(int $matchId, TournamentEntry $entry, string $side): void
    {
        $participant = MatchParticipant::where('match_id', $matchId)->where('side', $side)->first();

        if ($participant) {
            $participant->updateQuietly([
                'tournament_entry_id' => $entry->id,
                'club_id' => null,
                'goals_scored' => 0,
                'is_winner' => null,
            ]);
        } else {
            MatchParticipant::create([
                'match_id' => $matchId,
                'tournament_entry_id' => $entry->id,
                'side' => $side,
                'goals_scored' => 0,
            ]);
        }
    }

    /**
     * Handle tournament start: BYE single-player matches, set two-player matches to ready.
     */
    public function startTournament(Tournament $tournament): void
    {
        DB::transaction(function () use ($tournament) {
            $stages = $tournament->stages()->where('status', 'ongoing')->get();

            foreach ($stages as $stage) {
                $maxRound = GameMatch::where('tournament_stage_id', $stage->id)
                    ->where('bracket_position', '!=', '3rd_place')
                    ->max('round_number') ?? 1;

                for ($r = 1; $r <= $maxRound; $r++) {
                    $matches = GameMatch::where('tournament_stage_id', $stage->id)
                        ->where('round_number', $r)
                        ->where('bracket_position', '!=', '3rd_place')
                        ->orderBy('match_order')
                        ->get();

                    foreach ($matches as $match) {
                        if ($match->status === 'completed') continue;

                        $filled = MatchParticipant::where('match_id', $match->id)
                            ->whereNotNull('tournament_entry_id')
                            ->get();

                        if ($filled->count() === 0) {
                            if ($r === 1) {
                                $match->updateQuietly(['status' => 'completed', 'is_bye' => true]);
                            }
                            continue;
                        }

                        if ($filled->count() === 1 && $r === 1) {
                            $winner = $filled->first();
                            $match->updateQuietly(['status' => 'completed', 'is_bye' => true]);
                            $winner->updateQuietly(['is_winner' => true, 'goals_scored' => 0]);
                            $this->advanceWinner($match, $winner->entry);
                            continue;
                        }
                    }
                }

                // Cascade BYE wins for single-player matches in later rounds.
                // Only auto-BYE if ALL parent matches are completed — otherwise
                // the match is simply waiting for the other parent's winner.
                for ($r = 2; $r <= $maxRound; $r++) {
                    $pendingMatches = GameMatch::where('tournament_stage_id', $stage->id)
                        ->where('round_number', $r)
                        ->where('bracket_position', '!=', '3rd_place')
                        ->where('status', 'pending')
                        ->get();

                    foreach ($pendingMatches as $match) {
                        $parentMatches = GameMatch::where('tournament_stage_id', $stage->id)
                            ->where('next_match_id', $match->id)
                            ->get();

                        $allParentsCompleted = $parentMatches->every(
                            fn($p) => in_array($p->status, ['completed', 'walkover'])
                        );

                        if (!$allParentsCompleted) continue;

                        $filled = MatchParticipant::where('match_id', $match->id)
                            ->whereNotNull('tournament_entry_id')
                            ->get();

                        if ($filled->count() === 1) {
                            $winner = $filled->first();
                            $match->updateQuietly(['status' => 'completed', 'is_bye' => true]);
                            $winner->updateQuietly(['is_winner' => true, 'goals_scored' => 0]);
                            $this->advanceWinner($match, $winner->entry);
                        } elseif ($filled->count() === 2) {
                            $match->updateQuietly(['status' => 'ready']);
                        }
                    }
                }

                // Handle 3rd place match
                $thirdPlace = GameMatch::where('tournament_stage_id', $stage->id)
                    ->where('bracket_position', '3rd_place')
                    ->first();

                if ($thirdPlace) {
                    // Removed auto-update to 'ready' for 3rd place match
                }
            }
        });
    }

    /**
     * FIFO queue: assign PS units to ready matches.
     */
    public function processQueue(): void
    {
        $availableUnits = PsUnit::where('status', 'active')
            ->whereDoesntHave('matches', function ($query) {
                $query->whereIn('status', ['scheduled', 'ongoing']);
            })
            ->get();

        if ($availableUnits->isEmpty()) return;

        $readyMatches = GameMatch::where('status', 'ready')
            ->where('is_bye', false)
            ->orderBy('round_number')
            ->orderBy('id')
            ->get();

        foreach ($availableUnits as $unit) {
            if ($readyMatches->isEmpty()) break;
            $match = $readyMatches->shift();

            DB::transaction(function () use ($match, $unit) {
                $match->update([
                    'status' => 'scheduled',
                    'ps_unit_id' => $unit->id,
                    'scheduled_at' => Carbon::now(),
                ]);

                PsUnitSchedule::create([
                    'ps_unit_id' => $unit->id,
                    'match_id' => $match->id,
                    'booked_from' => Carbon::now(),
                    'status' => 'booked',
                ]);
            });
        }
    }
}
