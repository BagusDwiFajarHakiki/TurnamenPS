<x-filament-panels::page>
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield !important;
        }

        /* Light Mode defaults (applied globally on page) */
        :root, html {
            --bg-card: #ffffff;
            --bg-panel: #f9fafb;
            --bg-item: #f3f4f6;
            --bg-item-hover: #e5e7eb;
            --border-color: #e5e7eb;
            --text-main: #111827;
            --text-muted: #6b7280;
            --primary: #10b981; /* green */
            --secondary: #f43f5e;
            --bg-active: #e6f6eb;
            --border-active: #10b981;
            --shadow-active: rgba(16, 185, 129, 0.1);
            --input-bg: #ffffff;
            --input-border: #d1d5db;
            --input-color: #111827;
            --option-bg: #ffffff;
            --option-color: #111827;
        }

        /* Dark Mode overrides */
        .dark {
            --bg-card: #1a1b20;
            --bg-panel: #141517;
            --bg-item: #18191c;
            --bg-item-hover: #1f2025;
            --border-color: #282a30;
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
            --primary: #39d353; /* bright green */
            --secondary: #f43f5e;
            --bg-active: #0c2010;
            --border-active: #39d353;
            --shadow-active: rgba(57, 211, 83, 0.15);
            --input-bg: #1c1d22;
            --input-border: #343742;
            --input-color: #f4f4f5;
            --option-bg: #141517;
            --option-color: #f4f4f5;
        }

        /* Global dropdown and text input styling for this page */
        select,
        input[type="text"] {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--input-border) !important;
            color: var(--input-color) !important;
            border-radius: 8px !important;
            padding: 0.45rem 0.75rem !important;
            font-size: 0.85rem !important;
            outline: none !important;
            transition: all 0.2s ease !important;
        }

        select:focus,
        input[type="text"]:focus {
            border-color: var(--border-active) !important;
            box-shadow: 0 0 0 2px var(--shadow-active) !important;
        }

        select option {
            background-color: var(--option-bg) !important;
            color: var(--option-color) !important;
        }

        .quick-input-container {
            display: flex;
            gap: 2rem;
            align-items: stretch;
            width: 100%;
            flex-wrap: wrap;
        }

        .quick-input-container .sub-panel {
            background-color: var(--bg-panel) !important;
            border: 1px solid var(--border-color) !important;
            padding: 1.5rem !important;
            border-radius: 14px !important;
        }

        .quick-input-container .match-card {
            background-color: var(--bg-item) !important;
            border: 1px solid var(--border-color) !important;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            padding: 0.85rem 1rem;
            border-radius: 10px;
        }

        .quick-input-container .match-card:hover {
            background-color: var(--bg-item-hover) !important;
            border-color: var(--border-active) !important;
        }

        .quick-input-container .match-card.active {
            background-color: var(--bg-active) !important;
            border: 2px solid var(--border-active) !important;
            box-shadow: 0 4px 15px var(--shadow-active) !important;
        }

        .quick-input-container .score-input {
            background-color: var(--input-bg) !important;
            border: 2px solid var(--input-border) !important;
            color: var(--input-color) !important;
            font-size: 2.25rem !important;
            font-weight: 900 !important;
            text-align: center !important;
            border-radius: 12px !important;
            width: 76px !important;
            height: 76px !important;
            transition: all 0.2s ease !important;
            outline: none !important;
            padding: 0 !important;
        }

        .quick-input-container .score-input.home {
            color: var(--primary) !important;
        }

        .quick-input-container .score-input.away {
            color: var(--secondary) !important;
        }

        .quick-input-container .score-input.home:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--shadow-active) !important;
        }

        .quick-input-container .score-input.away:focus {
            border-color: var(--secondary) !important;
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.2) !important;
        }

        .quick-input-container .main-panel {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 16px !important;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06) !important;
            overflow: hidden !important;
            height: 100%;
        }

        .dark .quick-input-container .main-panel {
            box-shadow: 0 8px 30px rgba(0,0,0,0.4) !important;
        }

        .list-column-wrapper {
            flex: 1; 
            min-width: 280px; 
            display: flex; 
            flex-direction: column; 
            gap: 1rem;
        }
        
        .list-scroll-container {
            position: relative;
            flex-grow: 1;
        }
        
        .list-scroll-content {
            position: absolute;
            inset: 0;
            overflow-y: auto;
            padding-right: 0.5rem;
            display: flex; 
            flex-direction: column; 
            gap: 1rem;
        }
    </style>

    {{-- Tournament Header Selector --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div>
                <label for="page_tournament_select" style="font-weight: 800; font-size: 0.78rem; color: var(--text-muted); letter-spacing: 0.8px; text-transform: uppercase; display: block; margin-bottom: 0.15rem;">Pilih Turnamen</label>
                <select 
                    id="page_tournament_select"
                    wire:model.live="selectedTournamentId" 
                    style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 8px; padding: 0.35rem 2.25rem 0.35rem 0.75rem; font-size: 0.88rem; font-weight: 700; outline: none; cursor: pointer;"
                >
                    @foreach (\App\Models\Tournament::latest()->get() as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ ucfirst($t->status) }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); background: var(--bg-item); border: 1px solid var(--border-color); padding: 0.45rem 1rem; border-radius: 30px; display: flex; align-items: center; gap: 0.5rem;">
            <span style="color: var(--primary);">●</span> Mode Input Cepat (Split View)
        </div>
    </div>

    {{-- Main Split View Grid --}}
    <div class="quick-input-container">
        
        {{-- LEFT COLUMN: FIXTURES LIST (30% width) --}}
        <div class="list-column-wrapper">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--primary); letter-spacing: 0.8px; text-transform: uppercase; margin: 0;">
                    JADWAL
                </h3>

                <div>
                    <select 
                        id="page_round_select"
                        wire:model.live="selectedRound" 
                        style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 8px; padding: 0.25rem 2rem 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 700; outline: none; cursor: pointer;"
                    >
                        <option value="">Semua Babak</option>
                        @php
                            $allMatchesForDropdown = \App\Models\GameMatch::whereHas('stage', fn($q) => $q->where('tournament_id', $this->selectedTournamentId))->get();
                            $maxRoundNumForDropdown = $allMatchesForDropdown->filter(fn($m) => $m->bracket_position !== '3rd_place')->max('round_number') ?? 1;
                            $rounds = [];
                            foreach(range(1, $maxRoundNumForDropdown) as $r) {
                                $stagesLeft = $maxRoundNumForDropdown - $r;
                                if ($stagesLeft === 0) { $name = 'Final'; }
                                elseif ($stagesLeft === 1) { $name = 'Semifinal'; }
                                elseif ($stagesLeft === 2) { $name = 'Perempat Final'; }
                                else { $name = 'Babak ' . pow(2, $stagesLeft + 1); }
                                $rounds["round_{$r}"] = $name;
                            }
                            if ($allMatchesForDropdown->where('bracket_position', '3rd_place')->count() > 0) {
                                $rounds["3rd_place"] = "Perebutan Juara 3";
                            }
                        @endphp
                        @foreach($rounds as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @php
                $matchesQuery = \App\Models\GameMatch::whereHas('stage', fn($q) => $q->where('tournament_id', $this->selectedTournamentId))
                    ->with(['participants.entry.player', 'stage'])
                    ->orderBy('round_number')
                    ->orderBy('match_order');
                
                if ($this->selectedRound) {
                    if ($this->selectedRound === '3rd_place') {
                        $matchesQuery->where('bracket_position', '3rd_place');
                    } elseif (str_starts_with($this->selectedRound, 'round_')) {
                        $rNum = (int) str_replace('round_', '', $this->selectedRound);
                        $matchesQuery->where('round_number', $rNum)->where(function($q) {
                            $q->where('bracket_position', '!=', '3rd_place')->orWhereNull('bracket_position');
                        });
                    }
                }
                
                $matches = $matchesQuery->get();
                
                $allRegular = \App\Models\GameMatch::whereHas('stage', fn($q) => $q->where('tournament_id', $this->selectedTournamentId))
                    ->where(function($q) {
                        $q->where('bracket_position', '!=', '3rd_place')->orWhereNull('bracket_position');
                    })->get();
                $maxRoundNum = $allRegular->max('round_number') ?? 1;

                $thirdPlaceMatches = $matches->filter(fn($m) => $m->bracket_position === '3rd_place');
                $regularMatches = $matches->filter(fn($m) => $m->bracket_position !== '3rd_place');
                
                $preFinalMatches = $regularMatches->filter(fn($m) => $m->round_number < $maxRoundNum);
                $finalMatches = $regularMatches->filter(fn($m) => $m->round_number === $maxRoundNum);
                $groupedPreFinal = $preFinalMatches->groupBy('round_number');
            @endphp

            <div class="list-scroll-container">
                <div class="list-scroll-content">
                    {{-- Pre-final rounds (Babak, Perempat, Semifinal) --}}
                    @forelse ($groupedPreFinal as $roundNum => $roundMatches)
                    <div class="round-group">
                        <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-main); text-transform: uppercase; margin-bottom: 0.5rem; padding-left: 0.25rem;">
                            @php
                                $stagesLeft = $maxRoundNum - $roundNum;
                                if ($stagesLeft === 1) {
                                    $roundName = 'Semifinal';
                                } elseif ($stagesLeft === 2) {
                                    $roundName = 'Perempat Final';
                                } else {
                                    $teamsInRound = pow(2, $stagesLeft + 1);
                                    $roundName = "Babak {$teamsInRound}";
                                }
                            @endphp
                            {{ $roundName }}
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @foreach ($roundMatches->sortBy('match_order') as $m)
                                @php
                                    $home = $m->participants->where('side', 'home')->first();
                                    $away = $m->participants->where('side', 'away')->first();
                                    $isActive = $this->selectedMatchId === $m->id;

                                    $statusText = 'MENUNGGU';
                                    $itemStyle = 'border: 1px solid transparent; background: #1c1d22;';
                                    $badgeStyle = 'color: #fbbf24;';
                                    
                                    if ($isActive) {
                                        $itemStyle = 'border: 1px solid var(--primary); background: #232d26;';
                                    }

                                    if (in_array($m->status, ['completed', 'walkover'])) {
                                        $statusText = 'SELESAI';
                                        $badgeStyle = 'color: var(--danger);';
                                    } elseif ($m->status === 'ongoing') {
                                        $statusText = 'LIVE';
                                        $badgeStyle = 'color: var(--primary); font-weight: bold;';
                                    } elseif (in_array($m->status, ['scheduled', 'ready'])) {
                                        $statusText = 'MENUNGGU';
                                        $badgeStyle = 'color: #fbbf24;';
                                    }
                                @endphp

                                <div 
                                    wire:click="selectMatch({{ $m->id }})"
                                    class="match-card {{ $isActive ? 'active' : '' }}"
                                    style="border-radius: 8px; padding: 0.75rem 1rem; cursor: pointer; transition: all 0.2s; {{ $itemStyle }}"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='var(--primary)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='{{ $isActive ? 'var(--primary)' : 'transparent' }}';"
                                >
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                        <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted);">
                                            Match Order #{{ $m->match_order }}
                                        </span>
                                        <span style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.5px; {{ $badgeStyle }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>

                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-align: left; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                            {{ $home?->entry?->display_name ?? 'TBD' }}
                                        </div>
                                        
                                        @if (in_array($m->status, ['completed', 'walkover', 'ongoing']))
                                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--primary);">
                                                {{ $m->status === 'walkover' ? 'WO' : ($home?->goals_scored ?? 0) . ' - ' . ($away?->goals_scored ?? 0) }}
                                            </div>
                                        @else
                                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); opacity: 0.5;">
                                                VS
                                            </div>
                                        @endif

                                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                            {{ $away?->entry?->display_name ?? 'TBD' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    @endforelse

                    {{-- 3rd Place --}}
                    @foreach ($thirdPlaceMatches as $m)
                        @php
                            $home = $m->participants->where('side', 'home')->first();
                            $away = $m->participants->where('side', 'away')->first();
                            $isActive = $this->selectedMatchId === $m->id;

                            $statusText = 'MENUNGGU';
                            $itemStyle = 'border: 1px solid transparent; background: #1c1d22;';
                            $badgeStyle = 'color: #fbbf24;';

                            if ($isActive) {
                                $itemStyle = 'border: 1px solid var(--primary); background: #232d26;';
                            }

                            if (in_array($m->status, ['completed', 'walkover'])) {
                                $statusText = 'SELESAI';
                                $badgeStyle = 'color: var(--danger);';
                            } elseif ($m->status === 'ongoing') {
                                $statusText = 'LIVE';
                                $badgeStyle = 'color: var(--primary); font-weight: bold;';
                            } elseif (in_array($m->status, ['scheduled', 'ready'])) {
                                $statusText = 'MENUNGGU';
                                $badgeStyle = 'color: #fbbf24;';
                            }
                        @endphp
                        <div class="round-group">
                            <div style="font-size: 0.8rem; font-weight: 800; color: #f59e0b; text-transform: uppercase; margin-bottom: 0.5rem; padding-left: 0.25rem;">
                                Perebutan Juara 3
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div
                                    wire:click="selectMatch({{ $m->id }})"
                                    class="match-card {{ $isActive ? 'active' : '' }}"
                                    style="border-radius: 8px; padding: 0.75rem 1rem; cursor: pointer; transition: all 0.2s; {{ $itemStyle }}"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='var(--primary)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='{{ $isActive ? 'var(--primary)' : 'transparent' }}';"
                                >
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                        <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted);">
                                            Match Order #{{ $m->match_order }}
                                        </span>
                                        <span style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.5px; {{ $badgeStyle }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-align: left; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                            {{ $home?->entry?->display_name ?? 'TBD' }}
                                        </div>
                                        @if (in_array($m->status, ['completed', 'walkover', 'ongoing']))
                                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--primary);">
                                                {{ $m->status === 'walkover' ? 'WO' : ($home?->goals_scored ?? 0) . ' - ' . ($away?->goals_scored ?? 0) }}
                                            </div>
                                        @else
                                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); opacity: 0.5;">
                                                VS
                                            </div>
                                        @endif
                                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                            {{ $away?->entry?->display_name ?? 'TBD' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Final --}}
                    @foreach ($finalMatches as $m)
                        @php
                            $home = $m->participants->where('side', 'home')->first();
                            $away = $m->participants->where('side', 'away')->first();
                            $isActive = $this->selectedMatchId === $m->id;

                            $statusText = 'MENUNGGU';
                            $itemStyle = 'border: 1px solid transparent; background: #1c1d22;';
                            $badgeStyle = 'color: #fbbf24;';

                            if ($isActive) {
                                $itemStyle = 'border: 1px solid var(--primary); background: #232d26;';
                            }

                            if (in_array($m->status, ['completed', 'walkover'])) {
                                $statusText = 'SELESAI';
                                $badgeStyle = 'color: var(--danger);';
                            } elseif ($m->status === 'ongoing') {
                                $statusText = 'LIVE';
                                $badgeStyle = 'color: var(--primary); font-weight: bold;';
                            } elseif (in_array($m->status, ['scheduled', 'ready'])) {
                                $statusText = 'MENUNGGU';
                                $badgeStyle = 'color: #fbbf24;';
                            }
                        @endphp
                        <div class="round-group">
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 0.5rem; padding-left: 0.25rem;">
                                Final
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <div
                                    wire:click="selectMatch({{ $m->id }})"
                                    class="match-card {{ $isActive ? 'active' : '' }}"
                                    style="border-radius: 8px; padding: 0.75rem 1rem; cursor: pointer; transition: all 0.2s; {{ $itemStyle }}"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='var(--primary)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='{{ $isActive ? 'var(--primary)' : 'transparent' }}';"
                                >
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                        <span style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted);">
                                            Match Order #{{ $m->match_order }}
                                        </span>
                                        <span style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.5px; {{ $badgeStyle }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-align: left; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                            {{ $home?->entry?->display_name ?? 'TBD' }}
                                        </div>
                                        @if (in_array($m->status, ['completed', 'walkover', 'ongoing']))
                                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--primary);">
                                                {{ $m->status === 'walkover' ? 'WO' : ($home?->goals_scored ?? 0) . ' - ' . ($away?->goals_scored ?? 0) }}
                                            </div>
                                        @else
                                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); opacity: 0.5;">
                                                VS
                                            </div>
                                        @endif
                                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main); text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1;">
                                            {{ $away?->entry?->display_name ?? 'TBD' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($thirdPlaceMatches->isEmpty() && $finalMatches->isEmpty() && $groupedPreFinal->isEmpty())
                        <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted); font-size: 0.85rem; border: 1px dashed var(--border-color); border-radius: 8px;">
                            Tidak ada pertandingan ditemukan untuk turnamen ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: SCOREBOARD INPUT CONSOLE (70% width) --}}
        <div style="flex: 2; min-width: 500px; display: flex; flex-direction: column; gap: 1rem;">
            <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--primary); letter-spacing: 0.8px; text-transform: uppercase;">
                INPUT HASIL PERTANDINGAN
            </h3>

            @if ($this->selectedMatchId)
                @php
                    $activeMatch = \App\Models\GameMatch::with(['participants.entry.player', 'stage.tournament', 'psUnit'])->find($this->selectedMatchId);
                    $homePlayer = $activeMatch?->participants->where('side', 'home')->first();
                    $awayPlayer = $activeMatch?->participants->where('side', 'away')->first();
                    $allClubs = \App\Models\Club::orderBy('name')->get();
                    $allPsUnits = \App\Models\PsUnit::all();
                @endphp

                @if ($activeMatch)
                    <div class="main-panel">
                        
                        {{-- Meta Ribbon Header --}}
                        <div style="background: var(--bg-panel); border-bottom: 1px solid var(--border-color); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <span style="font-size: 0.72rem; font-weight: 800; color: var(--primary); letter-spacing: 1px; text-transform: uppercase; background: rgba(57, 211, 83, 0.1); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    {{ $activeMatch->stage->tournament->name }}
                                </span>
                                <h4 style="font-size: 1.05rem; font-weight: 800; margin-top: 0.4rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                                    @php
                                        $maxRound = $activeMatch->stage->matches->max('round_number') ?? 1;
                                        $sl = $maxRound - $activeMatch->round_number;
                                        if ($sl === 0) { $headerRoundName = 'Final'; }
                                        elseif ($sl === 1) { $headerRoundName = 'Semifinal'; }
                                        elseif ($sl === 2) { $headerRoundName = 'Perempat Final'; }
                                        else { $headerRoundName = 'Babak ' . pow(2, $sl + 1); }
                                    @endphp
                                    {{ $headerRoundName }} — Pertandingan Ke-{{ $activeMatch->match_order }}
                                </h4>
                            </div>
                            
                            @if ($activeMatch->psUnit)
                                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); background: var(--bg-item); border: 1px solid var(--border-color); padding: 0.35rem 0.75rem; border-radius: 6px;">
                                    {{ $activeMatch->psUnit->name }}
                                </div>
                            @endif
                        </div>

                        {{-- Main Input Form --}}
                        <form wire:submit.prevent="saveMatchResult" style="padding: 1.75rem; display: flex; flex-direction: column; gap: 1.5rem;">
                            
                            {{-- Scoreboard Panel / Walkover Panel --}}
                            @if ($this->status === 'walkover')
                                <div class="sub-panel" style="background: rgba(239,68,68,0.02) !important; border: 1px solid rgba(239,68,68,0.2) !important; display: flex; flex-direction: column; gap: 1rem;">
                                    <h4 style="font-weight: 800; font-size: 0.85rem; color: var(--danger); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                                        PENGATURAN WALKOVER (WO)
                                    </h4>
                                    
                                    <div>
                                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">Pilih Pemain yang Tidak Hadir / WO:</label>
                                        <select wire:model.live="noShowEntryId" style="width: 100%; outline: none;">
                                            <option value="">-- Pilih Pemain --</option>
                                            @if ($homePlayer)
                                                <option value="{{ $homePlayer->tournament_entry_id }}">{{ $homePlayer->entry->display_name }} (Home)</option>
                                            @endif
                                            @if ($awayPlayer)
                                                <option value="{{ $awayPlayer->tournament_entry_id }}">{{ $awayPlayer->entry->display_name }} (Away)</option>
                                            @endif
                                        </select>
                                        @error('noShowEntryId') <span style="color: var(--danger); font-size: 0.75rem; display: block; margin-top: 0.35rem;">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label style="display: block; font-size: 0.72rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">Alasan WO:</label>
                                        <input type="text" wire:model.live.debounce.500ms="walkoverReason" placeholder="Misal: Tidak hadir setelah dipanggil 3 kali" style="width: 100%;">
                                        @error('walkoverReason') <span style="color: var(--danger); font-size: 0.75rem; display: block; margin-top: 0.35rem;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @else
                                <div class="sub-panel" style="display: flex; align-items: stretch; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
                                    
                                    {{-- HOME BOX --}}
                                    <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem;">
                                        <span style="font-size: 0.65rem; font-weight: 800; color: var(--primary); background: rgba(57, 211, 83, 0.1); border: 1px solid rgba(57, 211, 83, 0.2); padding: 0.15rem 0.45rem; border-radius: 4px; letter-spacing: 0.5px;">HOME</span>
                                        
                                        <div style="font-weight: 800; font-size: 1rem; color: var(--text-main); margin-bottom: 0.25rem;">
                                            {{ $homePlayer?->entry?->display_name ?? 'Pemain 1' }}
                                        </div>
                                        
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.500ms="homeScore" 
                                            min="0" 
                                            required 
                                            class="score-input home"
                                        >
                                        @error('homeScore') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror

                                        <div style="width: 100%; margin-top: 0.5rem;">
                                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.25rem; text-align: left; text-transform: uppercase; letter-spacing: 0.5px;">Pilihan Klub:</label>
                                            <select wire:model.live="homeClubId" style="width: 100%;">
                                                <option value="">-- Pilih Klub --</option>
                                                @foreach ($allClubs as $club)
                                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('homeClubId') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    {{-- CENTER VS DIVIDER --}}
                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 80px; gap: 0.5rem;">
                                        <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(57, 211, 83, 0.1); border: 1px solid rgba(57, 211, 83, 0.3); display: flex; align-items: center; justify-content: center; font-weight: 900; color: var(--primary); font-size: 0.8rem; box-shadow: 0 0 8px rgba(57, 211, 83, 0.1);">
                                            VS
                                        </div>
                                        <span style="font-size: 0.65rem; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px;">SCORE</span>
                                    </div>

                                    {{-- AWAY BOX --}}
                                    <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem;">
                                        <span style="font-size: 0.65rem; font-weight: 800; color: var(--secondary); background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.1); padding: 0.15rem 0.45rem; border-radius: 4px; letter-spacing: 0.5px;">AWAY</span>
                                        
                                        <div style="font-weight: 800; font-size: 1rem; color: var(--text-main); margin-bottom: 0.25rem;">
                                            {{ $awayPlayer?->entry?->display_name ?? 'Pemain 2' }}
                                        </div>
                                        
                                        <input 
                                            type="number" 
                                            wire:model.live.debounce.500ms="awayScore" 
                                            min="0" 
                                            required 
                                            class="score-input away"
                                        >
                                        @error('awayScore') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror

                                        <div style="width: 100%; margin-top: 0.5rem;">
                                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.25rem; text-align: left; text-transform: uppercase; letter-spacing: 0.5px;">Pilihan Klub:</label>
                                            <select wire:model.live="awayClubId" style="width: 100%;">
                                                <option value="">-- Pilih Klub --</option>
                                                @foreach ($allClubs as $club)
                                                    <option value="{{ $club->id }}">{{ $club->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('awayClubId') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                </div>
                            @endif

                            {{-- Sub-settings: PlayStation Unit & Penalties --}}
                            <div class="sub-panel" style="display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap; padding: 1.25rem !important;">
                                <div style="flex: 1; min-width: 180px;">
                                    <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">STATUS PERTANDINGAN</label>
                                    <select wire:model.live="status" style="width: 100%;">
                                        <option value="pending">Pending</option>
                                        <option value="ready">Siap Dimainkan</option>
                                        <option value="scheduled">Terjadwal</option>
                                        <option value="ongoing">Sedang Main</option>
                                        <option value="completed">Selesai (Completed)</option>
                                        <option value="walkover">Walkover (WO)</option>
                                    </select>
                                </div>

                                <div style="flex: 1; min-width: 180px;">
                                    <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">PILIH UNIT PLAYSTATION</label>
                                    <select wire:model.live="psUnitId" style="width: 100%;">
                                        <option value="">-- Pilih Unit PS (Auto/FIFO) --</option>
                                        @foreach ($allPsUnits as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->type }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if ($this->status !== 'walkover')
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1.15rem;">
                                        <input type="checkbox" id="modal_decided_by_penalty" wire:model.live="decidedByPenalty" style="accent-color: var(--primary); cursor: pointer; width: 1.1rem; height: 1.1rem; border-radius: 4px;">
                                        <label for="modal_decided_by_penalty" style="font-weight: 700; font-size: 0.8rem; cursor: pointer; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.5px;">Adu Penalti (Sudden Death)</label>
                                    </div>
                                @endif
                            </div>

                            {{-- penalty inputs --}}
                            @if ($this->status !== 'walkover' && $this->decidedByPenalty)
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; background: rgba(57,211,83,0.03); border: 1px dashed rgba(57, 211, 83, 0.2); padding: 1.25rem; border-radius: 12px;">
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">SKOR PENALTI (HOME)</label>
                                        <input type="number" wire:model.live.debounce.500ms="penaltyScoreHome" min="0" required style="width: 100%; background: #1c1d22; border: 1px solid #343742; border-radius: 6px; padding: 0.45rem; text-align: center; font-size: 1.1rem; font-weight: 800; color: #39d353; outline: none;">
                                        @error('penaltyScoreHome') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.5px;">SKOR PENALTI (AWAY)</label>
                                        <input type="number" wire:model.live.debounce.500ms="penaltyScoreAway" min="0" required style="width: 100%; background: #1c1d22; border: 1px solid #343742; border-radius: 6px; padding: 0.45rem; text-align: center; font-size: 1.1rem; font-weight: 800; color: #f43f5e; outline: none;">
                                        @error('penaltyScoreAway') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- Upload Screenshot --}}
                            @if ($this->status !== 'walkover')
                                <div class="sub-panel" style="padding: 1.25rem !important;">
                                    <label style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">📸 UNGGAH SCREENSHOT / BUKTI MATCH</label>
                                    
                                    <div style="border: 2px dashed #343742; border-radius: 10px; padding: 1.75rem 1rem; text-align: center; position: relative; background: rgba(255,255,255,0.01); cursor: pointer; transition: border-color 0.2s;"
                                         onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='#343742'"
                                    >
                                        <input type="file" wire:model="paymentProof" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" accept="image/*">
                                        <svg style="width: 2rem; height: 2rem; color: var(--text-muted); margin: 0 auto 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-main);">
                                            {{ $this->paymentProof ? '✓ Gambar terpilih: ' . $this->paymentProof->getClientOriginalName() : 'Seret file atau Klik untuk mengunggah' }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.15rem;">Format JPG/PNG, ukuran maksimal 2MB</div>
                                        <div wire:loading wire:target="paymentProof" style="color: var(--primary); font-size: 0.75rem; margin-top: 0.35rem;">
                                            ⏳ Mengunggah berkas...
                                        </div>
                                    </div>
                                    @error('paymentProof') <span style="color: var(--danger); font-size: 0.75rem; display: block; margin-top: 0.35rem;">{{ $message }}</span> @enderror

                                    @if ($this->paymentProof)
                                        <div style="margin-top: 1rem; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; max-width: 240px; display: inline-block;">
                                            <img src="{{ $this->paymentProof->temporaryUrl() }}" style="max-width: 100%; display: block;">
                                        </div>
                                    @elseif ($this->existingProofPath)
                                        <div style="margin-top: 1rem;">
                                            <a href="{{ asset('storage/' . $this->existingProofPath) }}" target="_blank" style="display: inline-block; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; max-width: 240px;">
                                                <img src="{{ asset('storage/' . $this->existingProofPath) }}" style="max-width: 100%; display: block;" title="Klik untuk memperbesar">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Auto-save Status Indicator --}}
                            <div style="display: flex; gap: 0.85rem; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15); padding: 0.4rem 0.85rem; border-radius: 20px;">
                                    <span wire:loading.remove wire:target="homeScore,awayScore,homeClubId,awayClubId,status,psUnitId,decidedByPenalty,penaltyScoreHome,penaltyScoreAway,noShowEntryId,walkoverReason,paymentProof" style="color: #10b981;">✓ Auto-save aktif</span>
                                    <span wire:loading wire:target="homeScore,awayScore,homeClubId,awayClubId,status,psUnitId,decidedByPenalty,penaltyScoreHome,penaltyScoreAway,noShowEntryId,walkoverReason,paymentProof" style="color: #fbbf24;">⏳ Menyimpan...</span>
                                </div>
                            </div>

                        </form>

                    </div>
                @endif
            @else
                <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 16px; background: var(--bg-card); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem;">
                    <span style="font-size: 2.25rem;">🎯</span>
                    <h4 style="font-weight: 800; font-size: 0.95rem; color: var(--text-main); margin: 0;">Pilih Pertandingan Terlebih Dahulu</h4>
                    <p style="font-size: 0.8rem; max-width: 280px; margin: 0; line-height: 1.4;">Pilih salah satu pertandingan di kolom kiri untuk mulai menginput skor secara real-time.</p>
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>
