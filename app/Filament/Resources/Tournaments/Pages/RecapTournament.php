<?php

namespace App\Filament\Resources\Tournaments\Pages;

use App\Filament\Resources\Tournaments\TournamentResource;
use App\Models\Tournament;
use App\Models\TournamentPlayerAggregate;
use App\Models\MatchDispute;
use Filament\Resources\Pages\Page;

class RecapTournament extends Page
{
    protected static string $resource = TournamentResource::class;

    protected string $view = 'filament.resources.tournaments.pages.recap-tournament';

    public ?Tournament $record = null;

    public $juara1 = null;
    public $juara2 = null;
    public $juara3 = null;
    public $topScorers = [];
    public $topStreaks = [];
    public $popularClubs = [];
    public $bracketRounds = [];
    public $adminSummary = [];
    public $playerEntryNumbers = [];

    public function mount(int|string $record): void
    {
        $this->record = Tournament::findOrFail($record);
        $this->calculateRecap();
    }

    public function calculateRecap(): void
    {
        $tournament = $this->record;
        if (!$tournament) return;

        $this->juara1 = $tournament->aggregates()->where('rank_position', 1)->first()?->player?->name;
        $this->juara2 = $tournament->aggregates()->where('rank_position', 2)->first()?->player?->name;
        $this->juara3 = $tournament->aggregates()->where('rank_position', 3)->first()?->player?->name;

        $this->topScorers = $tournament->aggregates()
            ->with('player')
            ->where('total_goals_scored', '>', 0)
            ->orderByDesc('total_goals_scored')
            ->limit(5)
            ->get();

        $this->topStreaks = $tournament->aggregates()
            ->with('player')
            ->where('best_win_streak', '>', 0)
            ->orderByDesc('best_win_streak')
            ->limit(5)
            ->get();

        $entryIds = $tournament->entries()->pluck('id');
        $this->playerEntryNumbers = $tournament->entries()
            ->whereNotNull('entry_number')
            ->pluck('entry_number', 'player_id')
            ->toArray();

        $this->popularClubs = $tournament->entries()
            ->select('club_id', \DB::raw('COUNT(*) as usage_count'))
            ->whereNotNull('club_id')
            ->groupBy('club_id')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get()
            ->each(function ($item) {
                $item->setRelation('club', \App\Models\Club::find($item->club_id));
            });

        $totalPlayers = $tournament->entries()->distinct('player_id')->count('player_id');

        $disputesCount = MatchDispute::whereHas('match', function ($q) use ($tournament) {
            $q->where('tournament_id', $tournament->id);
        })->count();

        $woCount = $tournament->entries()
            ->where('status', 'active')
            ->where('walkover_count', '>', 0)
            ->count();

        $revenue = $tournament->entries()
            ->where('status', 'verified')
            ->count() * ($tournament->price_per_slot ?? 0);

        $this->adminSummary = [
            'total_players'     => $totalPlayers,
            'disputes_count'    => $disputesCount,
            'wo_count'          => $woCount,
            'estimated_revenue' => $revenue,
        ];

        $matches = $tournament->stages()->first()?->matches()->get() ?? collect();
        $rounds = $matches->groupBy('round_number')->sortKeys()->values()->all();
        $this->bracketRounds = [];
        foreach ($rounds as $roundMatches) {
            $this->bracketRounds[] = $roundMatches->values();
        }
    }
}
