<?php

namespace App\Filament\Resources\GameMatches\Pages;

use App\Filament\Resources\GameMatches\GameMatchResource;
use App\Models\Tournament;
use App\Models\GameMatch;
use App\Models\PsUnit;
use App\Models\Club;
use App\Models\MatchParticipant;
use App\Models\TournamentEntry;
use App\Services\TournamentService;
use Filament\Resources\Pages\Page;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Livewire\Attributes\Url;

class InputHasil extends Page
{
    use WithFileUploads;

    protected static string $resource = GameMatchResource::class;
    protected string $view = 'filament.resources.game-matches.list';

    #[Url]
    public ?int $selectedTournamentId = null;
    #[Url]
    public ?int $selectedMatchId = null;
    #[Url]
    public ?string $selectedRound = null;

    public $homeScore = 0;
    public $awayScore = 0;
    public $homeClubId = null;
    public $awayClubId = null;
    public bool $decidedByPenalty = false;
    public $penaltyScoreHome = null;
    public $penaltyScoreAway = null;
    public $psUnitId = null;
    public $paymentProof = null;
    public $existingProofPath = null;
    public $status = 'ready';

    public bool $showWoOptions = false;
    public $noShowEntryId = null;
    public $walkoverReason = '';

    protected bool $isAutoSaving = false;

    public function mount(): void
    {
        if (!$this->selectedTournamentId) {
            $latest = Tournament::latest()->first();
            if ($latest) {
                $this->selectedTournamentId = $latest->id;
            }
        }
        
        if ($this->selectedMatchId) {
            $this->selectMatch($this->selectedMatchId);
        } else {
            $this->autoSelectMatch();
        }
    }

    public function updatedSelectedTournamentId(): void
    {
        $this->selectedRound = null;
        $this->autoSelectMatch();
    }

    protected function autoSelectMatch(): void
    {
        if (!$this->selectedTournamentId) {
            $this->selectedMatchId = null;
            return;
        }

        $firstMatch = GameMatch::whereHas('stage', fn($q) => $q->where('tournament_id', $this->selectedTournamentId))
            ->whereIn('status', ['ongoing', 'scheduled', 'ready'])
            ->orderBy('round_number')
            ->orderBy('match_order')
            ->first();

        if ($firstMatch) {
            $this->selectMatch($firstMatch->id);
        } else {
            $anyMatch = GameMatch::whereHas('stage', fn($q) => $q->where('tournament_id', $this->selectedTournamentId))
                ->orderBy('round_number')
                ->orderBy('match_order')
                ->first();

            if ($anyMatch) {
                $this->selectMatch($anyMatch->id);
            } else {
                $this->selectedMatchId = null;
            }
        }
    }

    public function selectMatch(int $matchId): void
    {
        $this->selectedMatchId = $matchId;
        $this->showWoOptions = false;
        $this->noShowEntryId = null;
        $this->walkoverReason = '';
        $this->paymentProof = null;

        $match = GameMatch::with(['participants.entry.player', 'participants.club'])->find($matchId);
        if ($match) {
            $homePart = $match->participants->where('side', 'home')->first();
            $awayPart = $match->participants->where('side', 'away')->first();

            $this->homeScore = $homePart?->goals_scored ?? 0;
            $this->awayScore = $awayPart?->goals_scored ?? 0;
            $this->homeClubId = $homePart?->club_id;
            $this->awayClubId = $awayPart?->club_id;
            $this->decidedByPenalty = (bool) $match->decided_by_penalty;
            $this->penaltyScoreHome = $match->penalty_score_home;
            $this->penaltyScoreAway = $match->penalty_score_away;
            $this->psUnitId = $match->ps_unit_id;
            $this->existingProofPath = $match->result_proof_path;
            $this->status = $match->status;
            $this->noShowEntryId = $match->no_show_entry_id;
            $this->walkoverReason = $match->walkover_reason;
        }
    }

    // Auto-save hooks triggered on field changes
    public function updatedHomeScore(): void { $this->autoSave(); }
    public function updatedAwayScore(): void { $this->autoSave(); }
    public function updatedHomeClubId(): void { $this->autoSave(); }
    public function updatedAwayClubId(): void { $this->autoSave(); }
    public function updatedDecidedByPenalty(): void { $this->autoSave(); }
    public function updatedPenaltyScoreHome(): void { $this->autoSave(); }
    public function updatedPenaltyScoreAway(): void { $this->autoSave(); }
    public function updatedPsUnitId(): void { $this->autoSave(); }
    public function updatedStatus(): void { $this->autoSave(); }
    public function updatedNoShowEntryId(): void { $this->autoSave(); }
    public function updatedWalkoverReason(): void { $this->autoSave(); }

    public function updatedSelectedMatchId(): void
    {
        if ($this->selectedMatchId) {
            $this->selectMatch($this->selectedMatchId);
        } else {
            $this->homeScore = null;
            $this->awayScore = null;
            $this->status = 'pending';
        }
    }

    public function updatedPaymentProof(): void
    {
        if ($this->paymentProof) {
            $this->autoSave();
        }
    }

    /**
     * Save all current form state to DB, then explicitly resolve result.
     * No model events — everything is explicit.
     */
    public function autoSave(): void
    {
        if ($this->isAutoSaving) return;
        if (!$this->selectedMatchId) return;

        $match = GameMatch::find($this->selectedMatchId);
        if (!$match) return;

        $this->isAutoSaving = true;

        $originalStatus = $match->getOriginal('status');

        try {
            DB::transaction(function () use ($match, $originalStatus) {
                // 1. Upload proof if provided
                $proofPath = $match->result_proof_path;
                if ($this->paymentProof) {
                    if ($proofPath) {
                        Storage::disk('public')->delete($proofPath);
                    }
                    $proofPath = $this->paymentProof->store('match_results', 'public');
                    $this->existingProofPath = $proofPath;
                    $this->paymentProof = null;
                }

                // 2. Update participants (scores, clubs) — silently
                $homePart = $match->participants->where('side', 'home')->first();
                $awayPart = $match->participants->where('side', 'away')->first();

                if ($this->status === 'walkover' && $this->noShowEntryId) {
                    $homeIsNoShow = $homePart && $homePart->tournament_entry_id == $this->noShowEntryId;

                    if ($homePart) {
                        $homePart->updateQuietly([
                            'goals_scored' => $homeIsNoShow ? 0 : 3,
                            'is_winner' => !$homeIsNoShow,
                            'club_id' => null,
                        ]);
                    }
                    if ($awayPart) {
                        $awayPart->updateQuietly([
                            'goals_scored' => $homeIsNoShow ? 3 : 0,
                            'is_winner' => $homeIsNoShow,
                            'club_id' => null,
                        ]);
                    }
                } else {
                    if ($homePart) {
                        $homePart->updateQuietly([
                            'goals_scored' => $this->homeScore ?? 0,
                            'club_id' => $this->homeClubId ?: null,
                        ]);
                    }
                    if ($awayPart) {
                        $awayPart->updateQuietly([
                            'goals_scored' => $this->awayScore ?? 0,
                            'club_id' => $this->awayClubId ?: null,
                        ]);
                    }
                }

                // 3. Update match fields — silently
                $match->updateQuietly([
                    'status' => $this->status,
                    'decided_by_penalty' => $this->decidedByPenalty,
                    'penalty_score_home' => $this->decidedByPenalty ? $this->penaltyScoreHome : null,
                    'penalty_score_away' => $this->decidedByPenalty ? $this->penaltyScoreAway : null,
                    'ps_unit_id' => $this->psUnitId ?: null,
                    'result_proof_path' => $proofPath,
                    'no_show_entry_id' => $this->status === 'walkover' ? ($this->noShowEntryId ?: null) : null,
                    'walkover_reason' => $this->status === 'walkover' ? $this->walkoverReason : null,
                ]);

                // 4. Explicitly resolve — only when completed or walkover
                if (in_array($this->status, ['completed', 'walkover'])) {
                    $match->resolveResultAndAdvance();
                } elseif ($originalStatus !== null && in_array($originalStatus, ['completed', 'walkover']) && !in_array($this->status, ['completed', 'walkover'])) {
                    // Status changed FROM completed/walkover → revoke advancement
                    $match->revokeAdvancement();
                }
            });

            Notification::make()
                ->title('Tersimpan otomatis')
                ->success()
                ->duration(1500)
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menyimpan!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isAutoSaving = false;
        }
    }

    public function saveMatchResult(): void
    {
        $this->autoSave();
    }

    public function saveWalkover(): void
    {
        $this->autoSave();
    }
}
