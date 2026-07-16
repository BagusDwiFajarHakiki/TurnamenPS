<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tournament;
use App\Models\TournamentPlayerAggregate;
use App\Models\GameMatch;
use App\Models\TournamentStage;
use App\Models\Player;
use Illuminate\Support\Facades\DB;

class Home extends Component
{
    public $openTournaments = [];
    public $ongoingTournaments = [];
    public $topPlayers = [];
    public $latestTournament = null;

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        Tournament::where('status', 'registration')
            ->where('registration_end', '<=', now())
            ->where('tournament_end', '>', now())
            ->get()
            ->each->tryAutoGenerateBracket();

        Tournament::where('status', 'ongoing')
            ->where('tournament_start', '<=', now())
            ->where('tournament_end', '>', now())
            ->get()
            ->each->tryStartTournament();

        $this->loadOpenTournaments();
        $this->loadOngoingTournaments();
        $this->loadLatestTournament();
        $this->loadTopPlayers();
    }

    public function loadTopPlayers()
    {
        $this->topPlayers = Player::withSum('aggregates as total_wins', 'total_wins')
            ->withSum('aggregates as total_goals', 'total_goals_scored')
            ->withSum('aggregates as total_matches', 'total_matches_played')
            ->having('total_matches', '>', 0)
            ->orderByDesc('total_wins')
            ->orderByDesc('total_goals')
            ->take(5)
            ->get();
    }

    public function loadOpenTournaments()
    {
        // Assuming status 'registration' means open for registration.
        // We will also grab 'upcoming' if there are any.
        $this->openTournaments = Tournament::whereIn('status', ['registration', 'upcoming'])
            ->where('registration_end', '>=', now())
            ->withCount('entries')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function loadOngoingTournaments()
    {
        $tournaments = Tournament::where('status', 'ongoing')
            ->with('stages')
            ->orderByDesc('id')
            ->get();

        foreach ($tournaments as $tournament) {
            $tournament->topScorers = TournamentPlayerAggregate::where('tournament_id', $tournament->id)
                ->orderByDesc('total_goals_scored')
                ->orderByDesc('total_wins')
                ->with('player')
                ->limit(5) // Limit to top 5 for the landing page preview
                ->get();

            // Load Bagan Live for the active stage (or last stage if none active)
            $activeStage = $tournament->stages->whereIn('status', ['ongoing', 'upcoming'])->first() ?? $tournament->stages->last();
            
            $tournament->baganLiveMatches = collect();
            if ($activeStage) {
                // Fetch completed and ongoing matches for the bracket preview
                $matches = GameMatch::where('tournament_stage_id', $activeStage->id)
                    ->with(['participants.entry.player', 'participants.club'])
                    ->orderBy('round_number')
                    ->orderBy('bracket_position')
                    ->get();
                
                $tournament->baganLiveMatches = $matches->groupBy('round_number')
                    ->map(function ($roundMatches) {
                        return $roundMatches->sortBy('match_order')->map(function ($match) {
                            return [
                                'id' => $match->id,
                                'bracket_position' => $match->bracket_position,
                                'round_number' => $match->round_number,
                                'match_order' => $match->match_order,
                                'is_bye' => $match->is_bye,
                                'status' => $match->status,
                                'decided_by_penalty' => $match->decided_by_penalty,
                                'penalty_score_home' => $match->penalty_score_home,
                                'penalty_score_away' => $match->penalty_score_away,
                                'participants' => $match->participants
                                    ->sortBy(fn($p) => $p->side === 'home' ? 0 : 1)
                                    ->values()
                                    ->map(function ($part) {
                                        return [
                                            'player_name' => $part->entry ? trim(($part->entry->player?->name ?? 'BYE') . ' ' . ($part->entry->entry_number ?? '')) : 'BYE',
                                            'club_name' => $part->club?->name,
                                            'goals_scored' => $part->goals_scored,
                                            'is_winner' => $part->is_winner,
                                            'side' => $part->side,
                                            'tournament_entry_id' => $part->tournament_entry_id,
                                        ];
                                    })->values()->toArray(),
                            ];
                        })->toArray();
                    });
            }
            $tournament->upcomingMatches = collect();
            $tournament->completedMatches = collect();
            
                $stageIds = $tournament->stages->pluck('id')->toArray();
                if (!empty($stageIds)) {
                    $maxRoundByStage = GameMatch::whereIn('tournament_stage_id', $stageIds)
                        ->select('tournament_stage_id', DB::raw('MAX(round_number) as max_round'))
                        ->groupBy('tournament_stage_id')
                        ->pluck('max_round', 'tournament_stage_id')
                        ->toArray();

                    $tournament->upcomingMatches = GameMatch::whereIn('tournament_stage_id', $stageIds)
                        ->whereIn('status', ['ready', 'scheduled', 'ongoing'])
                        ->with(['participants.entry.player', 'participants.club', 'psUnit'])
                        ->orderBy('round_number')
                        ->orderBy('scheduled_at')
                        ->limit(10)
                        ->get();
                        
                    $tournament->completedMatches = GameMatch::whereIn('tournament_stage_id', $stageIds)
                        ->whereIn('status', ['completed', 'walkover'])
                        ->with(['participants.entry.player', 'participants.club', 'stage'])
                        ->orderByDesc('finished_at')
                        ->get()
                        ->each(function ($match) use ($maxRoundByStage) {
                            $maxRound = $maxRoundByStage[$match->tournament_stage_id] ?? 1;
                            if ($match->bracket_position === '3rd_place') {
                                $match->computedRoundName = app()->getLocale() == 'id' ? 'Perebutan Juara 3' : '3rd Place';
                            } else {
                                $stagesLeft = $maxRound - $match->round_number;
                                if ($stagesLeft === 0) {
                                    $match->computedRoundName = 'Final';
                                } elseif ($stagesLeft === 1) {
                                    $match->computedRoundName = 'Semifinal';
                                } elseif ($stagesLeft === 2) {
                                    $match->computedRoundName = app()->getLocale() == 'id' ? 'Perempat Final' : 'Quarter-final';
                                } else {
                                    $teamsInRound = pow(2, $stagesLeft + 1);
                                    $match->computedRoundName = app()->getLocale() == 'id' ? "Babak {$teamsInRound} Besar" : "Round of {$teamsInRound}";
                                }
                            }
                        });
                }
        }

        $this->ongoingTournaments = $tournaments;
    }

    public function loadLatestTournament()
    {
        $tournament = Tournament::withCount('entries')
            ->with('stages')
            ->orderByDesc('id')
            ->first();

        if (!$tournament) {
            $this->latestTournament = null;
            return;
        }

        $activeStage = $tournament->stages->whereIn('status', ['ongoing', 'upcoming'])->first() ?? $tournament->stages->last();

        $tournament->baganLiveMatches = collect();
        if ($activeStage) {
            $matches = GameMatch::where('tournament_stage_id', $activeStage->id)
                ->with(['participants.entry.player', 'participants.club'])
                ->orderBy('round_number')
                ->orderBy('bracket_position')
                ->get();

            $tournament->baganLiveMatches = $matches->groupBy('round_number')
                ->map(function ($roundMatches) {
                    return $roundMatches->sortBy('match_order')->map(function ($match) {
                        return [
                            'id' => $match->id,
                            'bracket_position' => $match->bracket_position,
                            'round_number' => $match->round_number,
                            'match_order' => $match->match_order,
                            'is_bye' => $match->is_bye,
                            'status' => $match->status,
                            'decided_by_penalty' => $match->decided_by_penalty,
                            'penalty_score_home' => $match->penalty_score_home,
                            'penalty_score_away' => $match->penalty_score_away,
                            'participants' => $match->participants
                                ->sortBy(fn($p) => $p->side === 'home' ? 0 : 1)
                                ->values()
                                ->map(function ($part) {
                                    return [
                                        'player_name' => $part->entry ? trim(($part->entry->player?->name ?? 'BYE') . ' ' . ($part->entry->entry_number ?? '')) : 'BYE',
                                        'club_name' => $part->club?->name,
                                        'goals_scored' => $part->goals_scored,
                                        'is_winner' => $part->is_winner,
                                        'side' => $part->side,
                                        'tournament_entry_id' => $part->tournament_entry_id,
                                    ];
                                })->values()->toArray(),
                        ];
                    })->toArray();
                });
        }

        $tournament->upcomingMatches = collect();
        $tournament->completedMatches = collect();

        $stageIds = $tournament->stages->pluck('id')->toArray();
        if (!empty($stageIds)) {
            $maxRoundByStage = GameMatch::whereIn('tournament_stage_id', $stageIds)
                ->select('tournament_stage_id', DB::raw('MAX(round_number) as max_round'))
                ->groupBy('tournament_stage_id')
                ->pluck('max_round', 'tournament_stage_id')
                ->toArray();

            $tournament->upcomingMatches = GameMatch::whereIn('tournament_stage_id', $stageIds)
                ->whereIn('status', ['ready', 'scheduled', 'ongoing'])
                ->with(['participants.entry.player', 'participants.club', 'psUnit'])
                ->orderBy('round_number')
                ->orderBy('scheduled_at')
                ->limit(10)
                ->get();

            $tournament->completedMatches = GameMatch::whereIn('tournament_stage_id', $stageIds)
                ->whereIn('status', ['completed', 'walkover'])
                ->with(['participants.entry.player', 'participants.club', 'stage'])
                ->orderByDesc('finished_at')
                ->get()
                ->each(function ($match) use ($maxRoundByStage) {
                    $maxRound = $maxRoundByStage[$match->tournament_stage_id] ?? 1;
                    if ($match->bracket_position === '3rd_place') {
                        $match->computedRoundName = app()->getLocale() == 'id' ? 'Perebutan Juara 3' : '3rd Place';
                    } else {
                        $stagesLeft = $maxRound - $match->round_number;
                        if ($stagesLeft === 0) {
                            $match->computedRoundName = 'Final';
                        } elseif ($stagesLeft === 1) {
                            $match->computedRoundName = 'Semifinal';
                        } elseif ($stagesLeft === 2) {
                            $match->computedRoundName = app()->getLocale() == 'id' ? 'Perempat Final' : 'Quarter-final';
                        } else {
                            $teamsInRound = pow(2, $stagesLeft + 1);
                            $match->computedRoundName = app()->getLocale() == 'id' ? "Babak {$teamsInRound} Besar" : "Round of {$teamsInRound}";
                        }
                    }
                });
        }

        $this->latestTournament = $tournament;
    }

    public function render()
    {
        return view('livewire.home')
            ->layout('components.layouts.app', ['title' => 'Infinity Boxzone - Turnamen PlayStation']);
    }
}
