<?php

namespace App\Livewire\Tournament;

use Livewire\Component;
use App\Models\Tournament;
use App\Models\TournamentStage;
use App\Models\TournamentPlayerAggregate;
use App\Models\GameMatch;
use App\Models\MatchParticipant;
use Illuminate\Support\Facades\DB;

class Show extends Component
{
    public ?Tournament $tournament = null;
    public $stages = [];
    public $activeStageId = null;
    public $leaderboard = [];
    public $upcomingMatches = [];
    public $completedMatches = [];
    public $rounds = [];
    public $baganLiveMatches = [];
    public $topScorers = [];
    public $popularClubsCombined = [];
    public $maxRoundNumber = 0;
    public $playerEntryNumbers = [];
    public $highlightedPlayerId = null;
    public $playersInStage = [];
    public $activeEntryIds = [];

    public function mount($slug)
    {
        $this->tournament = Tournament::where('slug', $slug)->first();

        if (!$this->tournament) {
            abort(404, 'Turnamen tidak ditemukan.');
        }

        $this->tournament->tryAutoGenerateBracket();
        $this->tournament->tryStartTournament();
        $this->tournament->refresh();

        $this->stages = $this->tournament->stages;
        $this->activeStageId = $this->stages->where('status', 'ongoing')->first()?->id ?? $this->stages->first()?->id;
            
        $this->loadStageData();
        $this->loadLeaderboard();
        $this->loadMatches();
        $this->loadTopScorers();
        $this->loadPopularClubsCombined();
    }

    public function refreshData()
    {
        $this->tournament->refresh();
        $this->loadStageData();
        $this->loadLeaderboard();
        $this->loadMatches();
        $this->loadTopScorers();
        $this->loadPopularClubsCombined();
    }

    public function selectStage($stageId)
    {
        $this->activeStageId = $stageId;
        $this->loadStageData();
    }

    public function loadStageData()
    {
        if (!$this->activeStageId) {
            $this->rounds = [];
            $this->baganLiveMatches = [];
            $this->maxRoundNumber = 0;
            return;
        }

        $stageMatches = GameMatch::where('tournament_stage_id', $this->activeStageId)
            ->with(['participants.entry.player', 'participants.club'])
            ->get();

        $maxRound = $stageMatches->max('round_number') ?? 0;
        $this->maxRoundNumber = $maxRound;
        $this->rounds = [];

        for ($r = 1; $r <= $maxRound; $r++) {
            $this->rounds[$r] = $stageMatches->where('round_number', $r)
                ->sortBy('match_order')
                ->values()
                ->map(function ($match) {
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
                })
                ->toArray();
        }

        $this->baganLiveMatches = $stageMatches->sortBy([
            ['round_number', 'asc'],
            ['match_order', 'asc']
        ])->values();

        $playerIds = $stageMatches->flatMap->participants->pluck('entry.player_id')->filter()->unique();
        $this->playerEntryNumbers = \App\Models\TournamentEntry::where('tournament_id', $this->tournament->id)
            ->whereIn('player_id', $playerIds)
            ->whereNotNull('entry_number')
            ->pluck('entry_number', 'player_id')
            ->toArray();
            
        $this->playersInStage = \App\Models\Player::whereIn('id', $playerIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
            
        $playerIdsArray = array_column($this->playersInStage, 'id');
        if ($this->highlightedPlayerId && !in_array($this->highlightedPlayerId, $playerIdsArray)) {
            $this->highlightedPlayerId = null;
        }
            
        $this->updateActiveEntryIds();
    }

    public function updatedHighlightedPlayerId()
    {
        $this->updateActiveEntryIds();
    }

    public function updateActiveEntryIds()
    {
        if (!$this->highlightedPlayerId || !$this->tournament) {
            $this->activeEntryIds = [];
            return;
        }

        $this->activeEntryIds = \App\Models\TournamentEntry::where('tournament_id', $this->tournament->id)
            ->where('player_id', $this->highlightedPlayerId)
            ->pluck('id')
            ->toArray();
    }

    public function loadLeaderboard()
    {
        if (!$this->tournament) return;

        $this->leaderboard = TournamentPlayerAggregate::where('tournament_id', $this->tournament->id)
            ->with('player')
            ->orderBy('rank_position')
            ->get();
    }

    public function loadMatches()
    {
        if (!$this->tournament) return;

        $stageIds = $this->stages->pluck('id')->toArray();

        $this->upcomingMatches = GameMatch::whereIn('tournament_stage_id', $stageIds)
            ->whereIn('status', ['ready', 'scheduled', 'ongoing'])
            ->with(['participants.entry.player', 'participants.club', 'psUnit'])
            ->orderBy('round_number')
            ->orderBy('scheduled_at')
            ->get();

        $this->completedMatches = GameMatch::whereIn('tournament_stage_id', $stageIds)
            ->whereIn('status', ['completed', 'walkover'])
            ->with(['participants.entry.player', 'participants.club'])
            ->orderByDesc('finished_at')
            ->get();
    }

    public function loadTopScorers()
    {
        if (!$this->tournament) return;

        $this->topScorers = TournamentPlayerAggregate::where('tournament_id', $this->tournament->id)
            ->orderByDesc('total_goals_scored')
            ->orderByDesc('total_wins')
            ->with('player')
            ->limit(10)
            ->get();
    }

    public function loadPopularClubsCombined()
    {
        $this->popularClubsCombined = MatchParticipant::whereNotNull('club_id')
            ->select('club_id', DB::raw('count(*) as usage_count'))
            ->groupBy('club_id')
            ->orderByDesc('usage_count')
            ->with('club')
            ->limit(5)
            ->get();
    }



    public function render()
    {
        return view('livewire.tournament.show')
            ->layout('components.layouts.app', [
                'title' => $this->tournament ? $this->tournament->name : 'No Tournament Active'
            ]);
    }
}
