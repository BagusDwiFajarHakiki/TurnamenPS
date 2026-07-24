<?php

namespace App\Livewire\Player;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tournament;
use App\Models\TournamentEntry;
use App\Models\GameMatch;
use App\Models\MatchDispute;
use App\Models\EntryBatch;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithFileUploads;

    public $player = null;
    public $activeEntries = [];
    public $myMatches = [];
    public $myMatchHistory = [];
    public $activeEntryIds = [];

    public $openTournaments = [];
    public $pendingPayments = [];

    // Bracket variables
    public $myTournaments = [];
    public $selectedBracketTournamentId = null;
    public $selectedBracketStageId = null;
    public $bracketRounds = [];
    public $bracketStages = [];
    public $bracketViewMode = 'bracket';
    public $historyViewMode = 'list';

    // Fields for raising disputes
    public $selectedMatchId = null;
    public $disputeReason = '';

    public $toastMessage = '';
    public $toastType = 'success';

    // Fields for buying slots
    public $selectedTournamentId = null;
    public $slot_count = 1;
    public $total_price = 0.00;
    public $payment_info = '';
    public $qris_image_path = null;
    public $payment_proof;
    public int $maxPurchasable = 1;
    public int $remainingOverall = 0;
    public int $playerCurrentTotal = 0;
    public int $purchaseStep = 1;
    public string $payment_method = 'qris';



    protected array $rules = [];

    public function mount()
    {
        if (!Auth::guard('player')->check()) {
            return redirect('/login');
        }

        $this->player = Auth::guard('player')->user();

        \App\Models\Tournament::where('status', 'registration')
            ->where('registration_end', '<=', now())
            ->where('tournament_end', '>', now())
            ->get()
            ->each->tryAutoGenerateBracket();

        \App\Models\Tournament::where('status', 'ongoing')
            ->where('tournament_start', '<=', now())
            ->where('tournament_end', '>', now())
            ->get()
            ->each->tryStartTournament();

        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // 1. Fetch active/verified tournament entries that still have scheduled matches (not yet eliminated)
        $this->activeEntries = TournamentEntry::where('player_id', $this->player->id)
            ->whereHas('tournament', function ($query) {
                $query->where('status', '!=', 'completed')
                      ->where('tournament_end', '>', now());
            })
            ->where(function ($query) {
                $query->whereHas('tournament', function ($sub) {
                    $sub->where('status', 'registration');
                })
                ->orWhereDoesntHave('matchParticipants')
                ->orWhereHas('matchParticipants.match', function ($sub) {
                    $sub->whereNotIn('status', ['completed', 'walkover']);
                });
            })
            ->with(['tournament', 'group'])
            ->get();

        // Get all entry IDs of the player (including eliminated ones) for matches and history queries
        $this->activeEntryIds = TournamentEntry::where('player_id', $this->player->id)->pluck('id')->toArray();

        // 2. Fetch active/scheduled/ongoing/ready matches involving this player
        $this->myMatches = GameMatch::whereHas('participants', function ($query) {
                $query->whereIn('tournament_entry_id', $this->activeEntryIds);
            })
            ->whereHas('stage.tournament', function ($query) {
                $query->whereIn('status', ['ongoing', 'registration']);
            })
            ->whereNotIn('status', ['completed', 'walkover'])
            ->with(['participants.entry.player', 'participants.club', 'psUnit', 'stage.tournament'])
            ->orderBy('round_number')
            ->get()
            ->each(function ($match) {
                if ($match->bracket_position === '3rd_place') {
                    $match->computedRoundName = app()->getLocale() == 'id' ? 'Posisi 3' : '3rd Place';
                    return;
                }
                $maxRound = GameMatch::where('tournament_stage_id', $match->tournament_stage_id)->max('round_number') ?? 1;
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
            });

        // 2b. Fetch completed/walkover matches involving this player for history
        $this->myMatchHistory = GameMatch::whereHas('participants', function ($query) {
                $query->whereIn('tournament_entry_id', $this->activeEntryIds);
            })
            ->whereIn('status', ['completed', 'walkover'])
            ->with(['participants.entry.player', 'participants.club', 'psUnit', 'stage.tournament'])
            ->orderByDesc('finished_at')
            ->get()
            ->each(function ($match) {
                if ($match->bracket_position === '3rd_place') {
                    $match->computedRoundName = app()->getLocale() == 'id' ? 'Posisi 3' : '3rd Place';
                    return;
                }
                $maxRound = GameMatch::where('tournament_stage_id', $match->tournament_stage_id)->max('round_number') ?? 1;
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
            });

        // 3. Fetch tournaments open for registration
        $this->openTournaments = Tournament::whereIn('status', ['registration', 'upcoming'])
            ->where('registration_end', '>=', now())
            ->where('tournament_end', '>', now())
            ->withCount('entries')
            ->orderBy('id', 'desc')
            ->get();
        // 4. Fetch all payments (batches) for history
        $this->pendingPayments = EntryBatch::where('player_id', $this->player->id)
            ->with('tournament')
            ->orderByDesc('id')
            ->get();
        // 5. Fetch tournaments the player is participating in (including ended/completed ones for history)
        $this->myTournaments = Tournament::whereHas('entries', function ($q) {
                $q->where('player_id', $this->player->id);
            })
            ->orderByDesc('id')
            ->get();

        if ($this->myTournaments->isNotEmpty() && !$this->selectedBracketTournamentId) {
            $this->selectBracketTournament($this->myTournaments->first()->id);
        }

    }

    public function showToast($message, $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
    }

    public function dismissToast()
    {
        $this->toastMessage = '';
        $this->toastType = 'success';
    }

    /**
     * Poll to check if any of the player's matches became "scheduled" and need a voice alert.
     */
    public function checkIncomingCalls()
    {
        if ($this->selectedTournamentId) {
            return;
        }

        $this->loadDashboardData();
        
        if ($this->selectedBracketStageId) {
            $this->loadBracketData();
        }

        $callingMatches = $this->myMatches->where('status', 'scheduled');

        foreach ($callingMatches as $match) {
            $sessionKey = "match_voiced_{$match->id}";
            if (!session()->has($sessionKey)) {
                $unitName = $match->psUnit ? $match->psUnit->name : 'TV';
                $location = $match->psUnit ? $match->psUnit->location : 'Unit';

                $lang = app()->getLocale();
                if ($lang == 'id') {
                    $message = "Panggilan untuk {$this->player->name}. Pertandingan Anda di {$unitName} lokasi {$location} siap dimainkan.";
                } else {
                    $message = "Call for {$this->player->name}. Your match on {$unitName} at {$location} is ready to be played.";
                }

                session()->put($sessionKey, true);
            }
        }
    }

    // Purchase slot modal actions
    public function selectTournamentForPurchase($tournamentId)
    {
        $t = Tournament::find($tournamentId);
        if (!$t) return;

        // Ensure registration is open and tournament has not ended
        if (!in_array($t->status, ['registration', 'upcoming']) || ($t->registration_end && now()->gt($t->registration_end)) || now()->gte($t->tournament_end)) {
            $this->showToast('Pendaftaran turnamen ini sudah ditutup atau turnamen telah berakhir.', 'error');
            return;
        }

        // Calculate limits
        $existingCount = TournamentEntry::where('tournament_id', $t->id)
            ->where('player_id', $this->player->id)
            ->count();
        
        $pendingCount = EntryBatch::where('tournament_id', $t->id)
            ->where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->sum('slot_count');
        
        $this->playerCurrentTotal = $existingCount + $pendingCount;
        $playerMaxPurchasable = $t->max_slot_per_player - $this->playerCurrentTotal;

        $verifiedCount = $t->entries()->count();
        $pendingOverall = EntryBatch::where('tournament_id', $t->id)
            ->where('status', 'pending')
            ->sum('slot_count');
        $this->remainingOverall = $t->max_entries - ($verifiedCount + $pendingOverall);

        $this->maxPurchasable = max(0, min($playerMaxPurchasable, $this->remainingOverall));

        if ($this->maxPurchasable <= 0) {
            if ($this->remainingOverall <= 0) {
                $this->showToast('Turnamen ini sudah penuh. Tidak ada slot yang tersedia.', 'error');
            } else {
                $this->showToast('Anda telah mencapai batas maksimal pembelian slot (' . $t->max_slot_per_player . ' slot) untuk turnamen ini.', 'error');
            }
            return;
        }

        $minSlots = $t->min_slots_per_player ?: 1;

        if ($this->maxPurchasable < $minSlots) {
            $this->showToast("Anda hanya bisa membeli {$this->maxPurchasable} slot lagi, tetapi minimal pembelian adalah {$minSlots} slot.", 'error');
            return;
        }

        $this->selectedTournamentId = $tournamentId;
        $this->slot_count = $minSlots;
        $this->payment_proof = null;
        $this->total_price = $minSlots * $t->price_per_slot;
        $this->payment_info = $t->payment_info ?: '';
        $this->qris_image_path = $t->qris_image_path;
        $this->purchaseStep = 1;
        $this->payment_method = 'qris';
    }

    public function updatedSlotCount()
    {
        if ($this->selectedTournamentId) {
            $t = Tournament::find($this->selectedTournamentId);
            if ($t) {
                $this->total_price = max(0, intval($this->slot_count)) * $t->price_per_slot;
            }
        }
    }

    public function proceedToPayment()
    {
        $t = Tournament::find($this->selectedTournamentId);
        if (!$t) return;

        $personalRemaining = max(0, $t->max_slot_per_player - $this->playerCurrentTotal);

        $minSlots = $t->min_slots_per_player ?: 1;

        $this->validate([
            'slot_count' => [
                'required',
                'integer',
                'min:' . $minSlots,
                function ($attribute, $value, $fail) use ($personalRemaining, $minSlots) {
                    if ($value < $minSlots) {
                        $fail("Minimal pembelian adalah {$minSlots} slot.");
                    }
                    if ($value > $personalRemaining) {
                        $fail("Jumlah slot melebihi batas pembelian Anda. Batas sisa Anda adalah {$personalRemaining} slot.");
                    }
                    if ($value > $this->remainingOverall) {
                        $fail("Jumlah slot melebihi sisa kuota turnamen yang tersedia. Sisa kuota adalah {$this->remainingOverall} slot.");
                    }
                }
            ],
        ]);

        $this->purchaseStep = 2;
    }

    public function submitPurchase()
    {
        $t = Tournament::find($this->selectedTournamentId);
        if (!$t) return;

        // Ensure registration is open and tournament has not ended
        if (!in_array($t->status, ['registration', 'upcoming']) || ($t->registration_end && now()->gt($t->registration_end)) || now()->gte($t->tournament_end)) {
            $this->showToast('Pendaftaran turnamen ini sudah ditutup atau turnamen telah berakhir.', 'error');
            return;
        }

        // Re-calculate limits to prevent race conditions
        $existingCount = TournamentEntry::where('tournament_id', $t->id)
            ->where('player_id', $this->player->id)
            ->count();
        
        $pendingCount = EntryBatch::where('tournament_id', $t->id)
            ->where('player_id', $this->player->id)
            ->where('status', 'pending')
            ->sum('slot_count');
        
        $this->playerCurrentTotal = $existingCount + $pendingCount;
        $playerMaxPurchasable = $t->max_slot_per_player - $this->playerCurrentTotal;

        $verifiedCount = $t->entries()->count();
        $pendingOverall = EntryBatch::where('tournament_id', $t->id)
            ->where('status', 'pending')
            ->sum('slot_count');
        $this->remainingOverall = $t->max_entries - ($verifiedCount + $pendingOverall);

        $this->maxPurchasable = max(0, min($playerMaxPurchasable, $this->remainingOverall));

        if ($this->maxPurchasable <= 0) {
            if ($this->remainingOverall <= 0) {
                $this->showToast('Turnamen ini sudah penuh. Tidak ada slot yang tersedia.', 'error');
            } else {
                $this->showToast('Anda telah mencapai batas maksimal pembelian slot (' . $t->max_slot_per_player . ' slot) untuk turnamen ini.', 'error');
            }
            $this->selectedTournamentId = null;
            return;
        }

        // Custom validation rules based on payment method
        $personalRemaining = max(0, $t->max_slot_per_player - $this->playerCurrentTotal);
        $minSlots = $t->min_slots_per_player ?: 1;
        $rules = [
            'slot_count' => [
                'required',
                'integer',
                'min:' . $minSlots,
                function ($attribute, $value, $fail) use ($personalRemaining, $minSlots) {
                    if ($value < $minSlots) {
                        $fail("Minimal pembelian adalah {$minSlots} slot.");
                    }
                    if ($value > $personalRemaining) {
                        $fail("Jumlah slot melebihi batas pembelian Anda. Batas sisa Anda adalah {$personalRemaining} slot.");
                    }
                    if ($value > $this->remainingOverall) {
                        $fail("Jumlah slot melebihi sisa kuota turnamen yang tersedia. Sisa kuota adalah {$this->remainingOverall} slot.");
                    }
                }
            ]
        ];

        if ($this->payment_method === 'qris') {
            $rules['payment_proof'] = 'required|image|max:10240';
        }

        $this->validate($rules);

        if ($this->payment_method === 'qris') {
            $proofPath = $this->payment_proof->store('payments', 'public');
        } else {
            $proofPath = null;
        }

        // Create transaction batch
        EntryBatch::create([
            'tournament_id' => $t->id,
            'player_id' => $this->player->id,
            'slot_count' => $this->slot_count,
            'total_price' => $this->total_price,
            'payment_proof_path' => $proofPath,
            'payment_method' => $this->payment_method,
            'status' => 'pending',
        ]);

        $this->showToast(
            app()->getLocale() == 'id'
                ? 'Pembelian slot berhasil diajukan!'
                : 'Slot purchase submitted successfully!'
        );

        $this->selectedTournamentId = null;
        $this->loadDashboardData();
    }

    public $bracketMyMatches = [];
    public function selectBracketTournament($tournamentId)
    {
        $this->selectedBracketTournamentId = $tournamentId;
        $t = Tournament::find($tournamentId);
        if ($t) {
            $this->bracketStages = $t->stages;
            $this->selectedBracketStageId = $this->bracketStages->where('status', 'ongoing')->first()?->id ?? $this->bracketStages->first()?->id;
            
            
            // Load user matches for this tournament
            $this->bracketMyMatches = GameMatch::whereHas('stage', function($q) use ($tournamentId) {
                $q->where('tournament_id', $tournamentId);
            })
            ->whereHas('participants', function($q) {
                $q->whereIn('tournament_entry_id', $this->activeEntryIds);
            })
            ->with(['participants.entry.player', 'participants.club', 'psUnit', 'stage.tournament'])
            ->orderByRaw("FIELD(status, 'ongoing', 'ready', 'scheduled', 'completed', 'walkover', 'pending')")
            ->orderBy('round_number', 'asc')
            ->orderBy('match_order', 'asc')
            ->orderBy('scheduled_at')
            ->orderByDesc('finished_at')
            ->get()
            ->each(function ($match) {
                if ($match->bracket_position === '3rd_place') {
                    $match->computedRoundName = app()->getLocale() == 'id' ? 'Posisi 3' : '3rd Place';
                    return;
                }
                $maxRound = GameMatch::where('tournament_stage_id', $match->tournament_stage_id)->max('round_number') ?? 1;
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
            });



            $this->loadBracketData();
        } else {
            $this->bracketStages = [];
            $this->selectedBracketStageId = null;
            $this->bracketRounds = [];
            $this->bracketMyMatches = [];
        }
    }

    public function selectBracketStage($stageId)
    {
        $this->selectedBracketStageId = $stageId;
        $this->loadBracketData();
    }

    public function loadBracketData()
    {
        if (!$this->selectedBracketStageId) {
            $this->bracketRounds = [];
            return;
        }

        $stageMatches = GameMatch::where('tournament_stage_id', $this->selectedBracketStageId)
            ->with(['participants.entry.player', 'participants.club'])
            ->get();

        $maxRound = $stageMatches->max('round_number') ?? 0;
        $this->bracketRounds = [];

        for ($r = 1; $r <= $maxRound; $r++) {
            $this->bracketRounds[$r] = $stageMatches->where('round_number', $r)
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
    }

    public function render()
    {
        return view('livewire.player.dashboard')
            ->layout('components.layouts.player', ['title' => __('Player Dashboard')]);
    }
}
