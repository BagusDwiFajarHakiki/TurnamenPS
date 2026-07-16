<div class="container" x-data="{ checkable: @entangle('hasCheckable'), checkedIn: @entangle('hasCheckedIn') }" wire:poll.3s="checkIncomingCalls">
    
    <!-- Top Header & Audio Settings -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: clamp(1.5rem, 4vw, 2.25rem); font-weight: 800;">
                {{ __('Player Dashboard') }}
            </h2>
            <p style="color: var(--text-muted); font-size: clamp(0.8rem, 2.5vw, 0.95rem);">{{ __('Selamat datang kembali,') }} <strong>{{ $player->name }}</strong></p>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <button onclick="togglePaymentDrawer(true)" class="btn btn-secondary" style="padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; border: 1px solid var(--border-color); cursor: pointer; white-space: nowrap;">
                💳 {{ app()->getLocale() == 'id' ? 'Status Pembayaran' : 'Payment Status' }}
            </button>
        </div>
    </div>

    <!-- PERSONAL STATISTICS (Horizontal) -->
    <div style="margin-bottom: 3rem;">
        <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; margin-bottom: 1.5rem; color: var(--accent);">
            📈 {{ __('Statistik Lintas Slot Anda') }}
        </h3>
        @php
            $aggregate = \App\Models\TournamentPlayerAggregate::where('player_id', $player->id)->first();
        @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; text-align: center;">
            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Total Goal</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--primary); margin-top: 0.25rem;">
                    {{ $aggregate?->total_goals_scored ?? 0 }}
                </div>
            </div>

            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Win Streak</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--accent); margin-top: 0.25rem;">
                    🔥 {{ $aggregate?->current_win_streak ?? 0 }}
                </div>
            </div>

            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Rasio Menang</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--secondary); margin-top: 0.25rem;">
                    @php
                        $total = $aggregate?->total_matches_played ?? 0;
                        $wins = $aggregate?->total_wins ?? 0;
                        echo $total > 0 ? round(($wins / $total) * 100) . '%' : '0%';
                    @endphp
                </div>
            </div>

            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Rekor Win Streak</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--secondary); margin-top: 0.25rem;">
                    🔥 {{ $aggregate?->best_win_streak ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <!-- TOURNAMENTS AVAILABLE FOR PURCHASE -->
    <div style="margin-bottom: 3rem;">
        <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; margin-bottom: 1.5rem; color: var(--primary);">
            📢 {{ app()->getLocale() == 'id' ? 'Pendaftaran Turnamen Dibuka' : 'Open Tournaments' }}
        </h3>
        
        <div class="grid grid-cols-2">
            @forelse ($openTournaments as $t)
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <span class="badge badge-success" style="margin-bottom: 0.75rem;">REGISTRATION OPEN</span>
                        <h4 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">{{ $t->name }}</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem;">
                            {{ __('Game') }}: {{ $t->game_title }}
                        </p>
                        @php
                            $verifiedCount = $t->entries()->count();
                            $pendingOverall = \App\Models\EntryBatch::where('tournament_id', $t->id)->where('status', 'pending')->sum('slot_count');
                            $availSlots = max(0, $t->max_entries - ($verifiedCount + $pendingOverall));
                        @endphp
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem;">
                            Slot Tersedia: <strong style="color: var(--primary);">{{ $availSlots }}</strong> dari <strong>{{ $t->max_entries }}</strong>
                        </p>
                        <p style="color: var(--danger); font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem;">
                            ⏳ {{ app()->getLocale() == 'id' ? 'Batas Pendaftaran' : 'Registration Deadline' }}: 
                            {{ $t->registration_end->format('d M Y H:i') }}
                        </p>
                        <div style="margin-bottom: 1.5rem;">
                            <span style="font-size: 0.9rem; color: var(--text-muted);">{{ __('Harga per Slot') }}:</span>
                            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-top: 0.25rem;">
                                Rp {{ number_format($t->price_per_slot, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <button wire:click="selectTournamentForPurchase({{ $t->id }})" class="btn btn-primary" style="width: 100%;">
                        🛒 {{ app()->getLocale() == 'id' ? 'Beli Slot / Ikuti' : 'Purchase Slot' }}
                    </button>
                </div>
            @empty
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--text-muted);">
                    {{ app()->getLocale() == 'id' ? 'Tidak ada turnamen yang membuka pendaftaran saat ini.' : 'No tournaments are accepting registrations right now.' }}
                </div>
            @endforelse
        </div>
    </div>


    <!-- ACTIVE SLOTS -->
    @if ($activeEntries->isNotEmpty())
        <div style="margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--accent);">
                    🎫 {{ __('Your Active Slots') }}
                </h3>
                <div x-show="checkable && !checkedIn" x-cloak>
                    <button wire:click="checkInAllSlots" class="btn btn-primary" style="padding: 0.6rem 1.5rem; border-radius: 10px; font-weight: 700; font-size: 0.9rem; white-space: nowrap;">
                        📌 Check In Semua Slot
                    </button>
                </div>
            </div>
            
            <div>
                <div class="grid grid-cols-2">
                    @foreach ($activeEntries as $entry)
                        @php
                            $t = $entry->tournament;
                            $leadMinutes = $t->check_in_open_minutes_before ?? 120;
                            $openTime = $t->tournament_start->copy()->subMinutes($leadMinutes);
                            
                            $isBeforeOpen = now()->lt($openTime);
                            $isAfterStart = now()->gte($t->tournament_start);
                            
                            $canCheckIn = ($entry->status === 'verified') && !$isBeforeOpen && !$isAfterStart;
                            $checkInMissed = ($entry->status === 'verified') && $isAfterStart;
                        @endphp
                        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; gap: 1rem;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                    <span style="font-size: 1.15rem; font-weight: 800; color: var(--text-main);">{{ $entry->display_name }}</span>
                                    @if ($entry->status === 'checked_in')
                                        <span class="badge badge-success">CHECKED IN</span>
                                    @elseif ($entry->status === 'verified')
                                        @if ($canCheckIn)
                                            <span class="badge badge-warning">SIAP CHECK-IN</span>
                                        @elseif ($checkInMissed)
                                            <span class="badge badge-danger">BELUM CHECK-IN (WO)</span>
                                        @else
                                            <span class="badge badge-info">TERVERIFIKASI</span>
                                        @endif
                                    @else
                                        <span class="badge {{ in_array($entry->status, ['active','champion']) ? 'badge-success' : 'badge-warning' }}">
                                            {{ strtoupper($entry->status) }}
                                        </span>
                                    @endif
                                </div>
                                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                    {{ __('Tournament') }}: <strong>{{ $t->name }}</strong>
                                </p>
                                <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                                    📅 Mulai Pertandingan: <span style="font-weight: 600;">{{ $t->tournament_start->format('d M Y H:i') }} WIB</span>
                                </p>
                                
                                @if ($entry->status === 'verified')
                                    @if ($canCheckIn)
                                        <p style="font-size: 0.8rem; color: var(--primary); font-weight: 600; margin-top: 0.5rem; background: rgba(57, 211, 83, 0.05); padding: 0.4rem; border-radius: 6px; border: 1px dashed rgba(57, 211, 83, 0.2);">
                                            📌 Check-in telah dibuka! Silakan check-in sebelum pukul {{ $t->tournament_start->format('H:i') }} WIB.
                                        </p>
                                    @elseif ($checkInMissed)
                                        <p style="font-size: 0.8rem; color: var(--danger); font-weight: 600; margin-top: 0.5rem; background: rgba(239, 68, 68, 0.05); padding: 0.4rem; border-radius: 6px; border: 1px dashed rgba(239, 68, 68, 0.2);">
                                            ⚠️ Anda tidak melakukan check-in tepat waktu. Slot ini dianggap BYE (tidak masuk bagan).
                                        </p>
                                    @else
                                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem; background: var(--bg-surface); padding: 0.4rem; border-radius: 6px; border: 1px solid var(--border-color);">
                                            🕒 Check-in dibuka mulai: <span style="font-weight: 600; color: var(--primary);">{{ $openTime->format('d M Y H:i') }} WIB</span> ({{ $leadMinutes }} menit sebelum tanding).
                                        </p>
                                    @endif
                                @endif

                                @if ($entry->status === 'champion')
                                    <p style="margin-top: 0.5rem; font-size: 1rem; font-weight: 700; color: gold;">🏆 Juara!</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- TOURNAMENT TREE BRACKET AND HISTORY -->
    <div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; color: var(--accent); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                🌳 {{ __('Riwayat & Bagan Turnamen Anda') }}
            </h3>

            {{-- View Mode Selector Toggles (Top Right Segmented Control) --}}
            @if ($this->selectedBracketTournamentId)
                <div style="display: flex; gap: 0.25rem; background: rgba(0,0,0,0.3); padding: 0.25rem; border-radius: 10px; border: 1px solid var(--border-color); align-items: center;">
                    <button 
                        type="button"
                        wire:click="$set('bracketViewMode', 'bracket')" 
                        style="
                            font-size: 0.75rem; 
                            padding: 0.45rem 1.15rem; 
                            border-radius: 8px; 
                            font-weight: 800; 
                            border: none;
                            background: {{ $this->bracketViewMode === 'bracket' ? 'var(--primary)' : 'transparent' }}; 
                            color: {{ $this->bracketViewMode === 'bracket' ? '#000' : 'var(--text-muted)' }}; 
                            cursor: pointer;
                            transition: all 0.2s;
                            display: flex;
                            align-items: center;
                            gap: 0.35rem;
                        "
                    >
                        🌳 Bagan Bracket
                    </button>
                    <button 
                        type="button"
                        wire:click="$set('bracketViewMode', 'list')" 
                        style="
                            font-size: 0.75rem; 
                            padding: 0.45rem 1.15rem; 
                            border-radius: 8px; 
                            font-weight: 800; 
                            border: none;
                            background: {{ $this->bracketViewMode === 'list' ? 'var(--primary)' : 'transparent' }}; 
                            color: {{ $this->bracketViewMode === 'list' ? '#000' : 'var(--text-muted)' }}; 
                            cursor: pointer;
                            transition: all 0.2s;
                            display: flex;
                            align-items: center;
                            gap: 0.35rem;
                        "
                    >
                        📋 Daftar Riwayat
                    </button>
                </div>
            @endif
        </div>

        @if (empty($this->myTournaments) || count($this->myTournaments) === 0)
            <div style="text-align: center; padding: 2.5rem; color: var(--text-muted); font-size: 0.9rem;">
                Anda belum memiliki riwayat turnamen atau pendaftaran yang terverifikasi.
            </div>
        @else
            {{-- Tournament Tab Selectors --}}
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                @foreach ($this->myTournaments as $t)
                    <button 
                        type="button"
                        wire:click="selectBracketTournament({{ $t->id }})" 
                        class="btn"
                        style="
                            font-size: 0.85rem; 
                            padding: 0.5rem 1.25rem; 
                            border-radius: 30px; 
                            font-weight: 700;
                            border: 1px solid {{ $this->selectedBracketTournamentId === $t->id ? 'var(--primary)' : 'var(--border-color)' }};
                            background: {{ $this->selectedBracketTournamentId === $t->id ? 'var(--primary)' : 'var(--bg-surface)' }};
                            color: {{ $this->selectedBracketTournamentId === $t->id ? '#000' : 'var(--text-main)' }};
                            cursor: pointer;
                            transition: all 0.2s;
                        "
                    >
                        🏆 {{ $t->name }}
                    </button>
                @endforeach
            </div>

            {{-- Selected Tournament Details & Bracket --}}
            @if ($this->selectedBracketTournamentId)
                @php
                    $selTournament = \App\Models\Tournament::find($this->selectedBracketTournamentId);
                @endphp
                @if ($selTournament)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        
                        {{-- Controls Header Bar (Fase Selectors only, since View Mode is now on top) --}}
                        @if (count($this->bracketStages) > 1)
                            <div style="display: flex; justify-content: flex-start; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                                {{-- Stage Selectors --}}
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                    <span style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Fase:</span>
                                    @foreach ($this->bracketStages as $stage)
                                        <button 
                                            type="button"
                                            wire:click="selectBracketStage({{ $stage->id }})" 
                                            style="
                                                font-size: 0.75rem; 
                                                padding: 0.35rem 0.85rem; 
                                                border-radius: 6px; 
                                                font-weight: 600; 
                                                cursor: pointer; 
                                                border: 1px solid {{ $this->selectedBracketStageId === $stage->id ? 'var(--primary)' : 'var(--border-color)' }}; 
                                                background: {{ $this->selectedBracketStageId === $stage->id ? 'rgba(57, 211, 83, 0.1)' : 'transparent' }}; 
                                                color: {{ $this->selectedBracketStageId === $stage->id ? 'var(--primary)' : 'var(--text-muted)' }};
                                                transition: all 0.2s;
                                            "
                                        >
                                            {{ $stage->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Mode: Tree Bracket View --}}
                        @if ($this->bracketViewMode === 'bracket')
                            @if (empty($this->bracketRounds) || count($this->bracketRounds) === 0)
                                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); font-size: 0.88rem; border: 1px dashed var(--border-color); border-radius: 12px; background: var(--bg-surface);">
                                    🌳 Bagan pertandingan untuk turnamen ini belum digenerate oleh admin.
                                </div>
                            @else
                                @include('_partials.bracket-tree', ['bracketRounds' => $this->bracketRounds, 'activeEntryIds' => $this->activeEntryIds])
                            @endif
                        @elseif ($this->bracketViewMode === 'list')
                            @php
                                $tournamentMatches = \App\Models\GameMatch::where('tournament_stage_id', $this->selectedBracketStageId)
                                    ->whereIn('status', ['completed', 'walkover'])
                                    ->with(['participants.entry.player', 'participants.club'])
                                    ->orderByDesc('finished_at')
                                    ->get();
                            @endphp
                            @if($tournamentMatches->isEmpty())
                                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); font-size: 0.88rem; border: 1px dashed var(--border-color); border-radius: 12px; background: var(--bg-surface);">
                                    📋 Belum ada riwayat pertandingan selesai untuk turnamen/fase ini.
                                </div>
                            @else
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
                                    @foreach ($tournamentMatches as $match)
                                        @php
                                            $home = $match->participants->where('side', 'home')->first();
                                            $away = $match->participants->where('side', 'away')->first();
                                            $isHomeMe = in_array($home?->tournament_entry_id, $this->activeEntryIds);
                                            $isAwayMe = in_array($away?->tournament_entry_id, $this->activeEntryIds);
                                            $isParticipant = $isHomeMe || $isAwayMe;
                                            
                                            $isWinner = ($isHomeMe && $home?->is_winner) || ($isAwayMe && $away?->is_winner);
                                        @endphp
                                        <div class="card" style="border-left: 4px solid {{ $isParticipant ? ($isWinner ? 'var(--primary)' : 'var(--danger)') : 'var(--border-color)' }}; opacity: 0.95; background: var(--bg-surface);">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                                @if ($isParticipant)
                                                    <span class="badge {{ $isWinner ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.7rem; padding: 0.15rem 0.5rem; font-weight: 800;">
                                                        {{ $isWinner ? 'MENANG' : 'KALAH' }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-info" style="font-size: 0.7rem; padding: 0.15rem 0.5rem; font-weight: 800; background: var(--border-color); color: var(--text-muted);">
                                                        SELESAI
                                                    </span>
                                                @endif
                                                <span style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem;">
                                                    @php
                                                        $maxRounds = $tournamentMatches->max('round_number') ?? 1;
                                                        $stagesLeft = $maxRounds - $match->round_number;
                                                        if ($stagesLeft === 0) {
                                                            $roundLabel = 'Final';
                                                        } elseif ($stagesLeft === 1) {
                                                            $roundLabel = 'Semifinal';
                                                        } elseif ($stagesLeft === 2) {
                                                            $roundLabel = 'Perempat Final';
                                                        } else {
                                                            $teamsInRound = pow(2, $stagesLeft + 1);
                                                            $roundLabel = "Babak {$teamsInRound} Besar";
                                                        }
                                                    @endphp
                                                    {{ $roundLabel }} | Pos: {{ $match->bracket_position }}
                                                </span>
                                            </div>

                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 1rem;">
                                                <div style="text-align: center; flex: 1; overflow: hidden;">
                                                    <span style="font-weight: {{ $isHomeMe ? '800' : '600' }}; color: {{ $isHomeMe ? 'var(--primary)' : 'var(--text-main)' }}; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $home?->entry?->display_name ?? 'TBD' }}
                                                    </span>
                                                    @if ($home?->club)
                                                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                            🏟 {{ $home->club->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <span style="color: var(--text-muted); font-size: 0.8rem; padding: 0 0.5rem; font-weight: bold;">VS</span>
                                                <div style="text-align: center; flex: 1; overflow: hidden;">
                                                    <span style="font-weight: {{ $isAwayMe ? '800' : '600' }}; color: {{ $isAwayMe ? 'var(--primary)' : 'var(--text-main)' }}; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $away?->entry?->display_name ?? 'TBD' }}
                                                    </span>
                                                    @if ($away?->club)
                                                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                            🏟 {{ $away->club->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                                <div>
                                                    <span style="font-size: 1.1rem; font-weight: 850; color: var(--text-main);">
                                                        {{ __('Skor') }}: {{ $home?->goals_scored }} - {{ $away?->goals_scored }}
                                                    </span>
                                                </div>

                                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                                    @if ($isParticipant && $match->status === 'completed' && !\App\Models\MatchDispute::where('match_id', $match->id)->whereIn('raised_by_entry_id', $this->activeEntryIds)->exists())
                                                        <button wire:click="initiateDispute({{ $match->id }})" class="btn btn-secondary" style="padding: 0.35rem 0.8rem; font-size: 0.75rem; color: var(--danger); border-color: rgba(239, 68, 68, 0.4); font-weight: 700; cursor: pointer;">
                                                            ⚠️ {{ __('Raise Dispute') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            @endif
        @endif
    </div>

    <!-- MATCH SCHEDULES & STATS -->
    <div style="display: flex; flex-direction: column; gap: 3rem;">
        
        <!-- Matches -->
        <div>
            <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; margin-bottom: 1.5rem; color: var(--secondary);">
                ⚔️ {{ __('Your Matches') }}
            </h3>
            
            <div class="grid grid-cols-2-strict" style="gap: 1.25rem;">
                @forelse ($myMatches as $match)
                    @php
                        $home = $match->participants->where('side', 'home')->first();
                        $away = $match->participants->where('side', 'away')->first();
                        $isHomeMe = in_array($home?->tournament_entry_id, $activeEntryIds);
                        
                        // Check if no-show deadline warnings apply
                        $showDeadlineWarning = false;
                        $remainingMinutes = 0;
                        if ($match->status === 'ready') {
                            $t = $match->stage?->tournament;
                            if ($t && $t->no_show_deadline_minutes) {
                                $deadline = $match->updated_at->copy()->addMinutes($t->no_show_deadline_minutes);
                                $remainingMinutes = now()->diffInSeconds($deadline) / 60;
                                $remainingMinutes = max(0, (int) floor($remainingMinutes));
                                $showDeadlineWarning = true;
                            }
                        }
                    @endphp
                    <div class="card" style="border-left: 4px solid {{ $match->status === 'ongoing' ? 'var(--primary)' : ($match->status === 'completed' ? 'var(--accent)' : 'var(--border-color)') }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span class="badge {{ $match->status === 'ongoing' ? 'badge-success' : ($match->status === 'completed' ? 'badge-info' : 'badge-warning') }}">
                                {{ strtoupper($match->status) }}
                            </span>
                            <span style="font-weight: 600; color: var(--text-muted); font-size: 0.82rem;">
                                🏆 {{ $match->stage?->tournament?->name }}
                            </span>
                        </div>

                        <div class="match-score-row" style="display: flex; align-items: center; margin-bottom: 1rem; font-size: 1.05rem;">
                            <div class="match-name-left" style="flex: 1; min-width: 0; text-align: left;">
                                <span style="font-weight: {{ $isHomeMe ? '700' : '400' }}; color: {{ $isHomeMe ? 'var(--primary)' : 'var(--text-main)' }}; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $home?->entry?->display_name ?? 'TBD' }}
                                </span>
                                @if ($home?->club)
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        🏟 {{ $home->club->name }}
                                    </div>
                                @endif
                            </div>
                            <div class="match-score-center" style="flex-shrink: 0; text-align: center; padding: 0 0.6rem; min-width: 70px;">
                                @if (in_array($match->status, ['completed', 'walkover']))
                                    <div style="font-weight: 800; font-size: 1.15rem; letter-spacing: 1px;">{{ $home?->goals_scored ?? '-' }} - {{ $away?->goals_scored ?? '-' }}</div>
                                    @if($match->decided_by_penalty)
                                        <div style="font-size: 0.6rem; letter-spacing: 1px; color: var(--text-muted);">({{ $match->penalty_score_home }}) - ({{ $match->penalty_score_away }})</div>
                                    @endif
                                @else
                                    <div style="font-weight: 800; font-size: 1.15rem; letter-spacing: 1px; color: var(--text-muted);">VS</div>
                                @endif
                                <div style="font-size: 0.65rem; font-weight: 600; color: var(--text-muted); margin-top: 0.15rem;">{{ $match->computedRoundName ?? $match->stage?->name }}</div>
                                @if ($match->psUnit)
                                    <div style="font-size: 0.6rem; color: var(--primary); margin-top: 0.1rem;">🎮 {{ $match->psUnit->name }}</div>
                                @endif
                            </div>
                            <div class="match-name-right" style="flex: 1; min-width: 0; text-align: right;">
                                <span style="font-weight: {{ !$isHomeMe ? '700' : '400' }}; color: {{ !$isHomeMe ? 'var(--primary)' : 'var(--text-main)' }}; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $away?->entry?->display_name ?? 'TBD' }}
                                </span>
                                @if ($away?->club)
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        🏟 {{ $away->club->name }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap; gap: 0.5rem;">

                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                @if ($match->status === 'completed' && !\App\Models\MatchDispute::where('match_id', $match->id)->whereIn('raised_by_entry_id', $activeEntryIds)->exists())
                                    <button wire:click="initiateDispute({{ $match->id }})" class="btn btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.85rem; color: var(--danger); border-color: rgba(239, 68, 68, 0.4);">
                                        ⚠️ {{ __('Raise Dispute') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($showDeadlineWarning)
                            <div style="margin-top: 0.75rem; font-size: 0.8rem; border-radius: 6px; padding: 0.5rem; background: {{ $remainingMinutes > 0 ? 'rgba(217, 119, 6, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; border: 1px solid {{ $remainingMinutes > 0 ? 'var(--warning)' : 'var(--danger)' }}; color: {{ $remainingMinutes > 0 ? 'var(--warning)' : 'var(--danger)' }}; font-weight: 700; text-align: center;">
                                @if ($remainingMinutes > 0)
                                    ⚠️ PANGGILAN PERTANDINGAN: Harap segera menuju ke console Anda! Sisa waktu tunggu: {{ $remainingMinutes }} menit sebelum Anda dianggap WO (kalah otomatis).
                                @else
                                    🚨 BATAS WAKTU HABIS: Waktu tunggu {{ $match->stage?->tournament?->no_show_deadline_minutes }} menit telah terlampaui. Anda dapat didiskualifikasi/WO oleh admin sewaktu-waktu.
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--text-muted);">
                        {{ __('Belum ada pertandingan terjadwal untuk Anda.') }}
                    </div>
                @endforelse
            </div>

            {{-- Personal Match History (Completed/Walkover) --}}
            @if (count($myMatchHistory) > 0)
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        📜 {{ __('Riwayat Pertandingan Anda') }}
                    </h3>

                    {{-- View Mode Selector Toggles for History --}}
                    <div style="display: flex; gap: 0.25rem; background: rgba(0,0,0,0.3); padding: 0.25rem; border-radius: 8px; border: 1px solid var(--border-color); align-items: center;">
                        <button 
                            type="button"
                            wire:click="$set('historyViewMode', 'list')" 
                            style="
                                font-size: 0.72rem; 
                                padding: 0.4rem 0.85rem; 
                                border-radius: 6px; 
                                font-weight: 800; 
                                border: none;
                                background: {{ $this->historyViewMode === 'list' ? 'var(--primary)' : 'transparent' }}; 
                                color: {{ $this->historyViewMode === 'list' ? '#000' : 'var(--text-muted)' }}; 
                                cursor: pointer;
                                transition: all 0.2s;
                            "
                        >
                            📋 List
                        </button>
                        <button 
                            type="button"
                            wire:click="$set('historyViewMode', 'bracket')" 
                            style="
                                font-size: 0.72rem; 
                                padding: 0.4rem 0.85rem; 
                                border-radius: 6px; 
                                font-weight: 800; 
                                border: none;
                                background: {{ $this->historyViewMode === 'bracket' ? 'var(--primary)' : 'transparent' }}; 
                                color: {{ $this->historyViewMode === 'bracket' ? '#000' : 'var(--text-muted)' }}; 
                                cursor: pointer;
                                transition: all 0.2s;
                            "
                        >
                            🌳 Bracket
                        </button>
                    </div>
                </div>
                
                {{-- Mode 1: List View (Daftar Riwayat Kartu Sengketa) --}}
                @if ($this->historyViewMode === 'list')
                    <div class="grid grid-cols-2-strict" style="gap: 1.25rem;">
                        @foreach ($myMatchHistory as $match)
                            @php
                                $home = $match->participants->where('side', 'home')->first();
                                $away = $match->participants->where('side', 'away')->first();
                                $isHomeMe = in_array($home?->tournament_entry_id, $activeEntryIds);
                                
                                // Check if player won or lost
                                $isWinner = ($isHomeMe && $home?->is_winner) || (!$isHomeMe && $away?->is_winner);
                            @endphp
                            <div class="card" style="border-left: 4px solid {{ $isWinner ? 'var(--primary)' : 'var(--danger)' }}; opacity: 0.85; background: var(--bg-surface);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                    <span class="badge {{ $isWinner ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.72rem; padding: 0.15rem 0.5rem; font-weight: 800;">
                                        {{ $isWinner ? 'MENANG' : 'KALAH' }}
                                    </span>
                                    <span style="font-weight: 600; color: var(--text-muted); font-size: 0.82rem; max-width: 70%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        🏆 {{ $match->stage?->tournament?->name }}
                                    </span>
                                </div>

                                <div class="match-score-row" style="display: flex; align-items: center; margin-bottom: 1rem; font-size: 1.05rem;">
                                    <div class="match-name-left" style="flex: 1; min-width: 0; text-align: left;">
                                        <span style="font-weight: {{ $isHomeMe ? '800' : '500' }}; color: {{ $isHomeMe ? 'var(--primary)' : 'var(--text-main)' }}; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $home?->entry?->display_name ?? 'TBD' }}
                                        </span>
                                        @if ($home?->club)
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                🏟 {{ $home->club->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="match-score-center" style="flex-shrink: 0; text-align: center; padding: 0 0.6rem; min-width: 70px;">
                                        <div style="font-weight: 850; font-size: 1.15rem; letter-spacing: 1px; color: var(--text-main);">{{ $home?->goals_scored ?? '-' }} - {{ $away?->goals_scored ?? '-' }}</div>
                                        @if($match->decided_by_penalty)
                                            <div style="font-size: 0.6rem; letter-spacing: 1px; color: var(--text-muted);">({{ $match->penalty_score_home }}) - ({{ $match->penalty_score_away }})</div>
                                        @endif
                                        <div style="font-size: 0.65rem; font-weight: 600; color: var(--text-muted); margin-top: 0.15rem;">{{ $match->computedRoundName ?? $match->stage?->name }}</div>
                                    </div>
                                    <div class="match-name-right" style="flex: 1; min-width: 0; text-align: right;">
                                        <span style="font-weight: {{ !$isHomeMe ? '800' : '500' }}; color: {{ !$isHomeMe ? 'var(--primary)' : 'var(--text-main)' }}; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $away?->entry?->display_name ?? 'TBD' }}
                                        </span>
                                        @if ($away?->club)
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                🏟 {{ $away->club->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-end; align-items: center;">
                                    @if ($match->status === 'completed' && !\App\Models\MatchDispute::where('match_id', $match->id)->whereIn('raised_by_entry_id', $activeEntryIds)->exists())
                                        <button wire:click="initiateDispute({{ $match->id }})" class="btn btn-secondary" style="padding: 0.35rem 0.8rem; font-size: 0.8rem; color: var(--danger); border-color: rgba(239, 68, 68, 0.4); font-weight: 700; cursor: pointer;">
                                            ⚠️ {{ __('Ajukan Sengketa') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                {{-- Mode 2: Tree Bracket View (Bagan Turnamen) --}}
                @elseif ($this->historyViewMode === 'bracket')
                    @if (empty($this->myTournaments) || count($this->myTournaments) === 0)
                        <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.85rem; border: 1px dashed var(--border-color); border-radius: 8px;">
                            Belum ada riwayat turnamen terdaftar.
                        </div>
                    @else
                        {{-- Select Tournament inside Bracket mode --}}
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                            @foreach ($this->myTournaments as $t)
                                <button 
                                    type="button"
                                    wire:click="selectBracketTournament({{ $t->id }})" 
                                    style="
                                        font-size: 0.72rem; 
                                        padding: 0.35rem 0.75rem; 
                                        border-radius: 20px; 
                                        font-weight: 700;
                                        border: 1px solid {{ $this->selectedBracketTournamentId === $t->id ? 'var(--primary)' : 'var(--border-color)' }};
                                        background: {{ $this->selectedBracketTournamentId === $t->id ? 'rgba(57, 211, 83, 0.12)' : 'transparent' }};
                                        color: {{ $this->selectedBracketTournamentId === $t->id ? 'var(--primary)' : 'var(--text-muted)' }};
                                        cursor: pointer;
                                    "
                                >
                                    🏆 {{ $t->name }}
                                </button>
                            @endforeach
                        </div>

                        @if ($this->selectedBracketTournamentId)
                            {{-- Stage Selectors --}}
                            @if (count($this->bracketStages) > 1)
                                <div style="display: flex; gap: 0.35rem; margin-bottom: 1rem; flex-wrap: wrap; align-items: center;">
                                    @foreach ($this->bracketStages as $stage)
                                        <button 
                                            type="button"
                                            wire:click="selectBracketStage({{ $stage->id }})" 
                                            style="
                                                font-size: 0.68rem; 
                                                padding: 0.35rem 0.7rem; 
                                                border-radius: 6px; 
                                                font-weight: 600; 
                                                cursor: pointer; 
                                                border: 1px solid {{ $this->selectedBracketStageId === $stage->id ? 'var(--primary)' : 'var(--border-color)' }}; 
                                                background: {{ $this->selectedBracketStageId === $stage->id ? 'rgba(57, 211, 83, 0.1)' : 'transparent' }}; 
                                                color: {{ $this->selectedBracketStageId === $stage->id ? 'var(--primary)' : 'var(--text-muted)' }};
                                            "
                                        >
                                            {{ $stage->name }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Tree Bracket Render --}}
                            @if (empty($this->bracketRounds) || count($this->bracketRounds) === 0)
                                <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.85rem; border: 1px dashed var(--border-color); border-radius: 8px;">
                                    Bagan pertandingan belum digenerate.
                                </div>
                            @else
                                @php
                                    $thirdPlaceMatchHist = null;
                                    $filteredHistRounds = [];
                                    foreach ($this->bracketRounds as $rNum => $rMatches) {
                                        $filtered = array_values(array_filter($rMatches, function ($m) use (&$thirdPlaceMatchHist) {
                                            if ($m['bracket_position'] === '3rd_place') {
                                                $thirdPlaceMatchHist = $m;
                                                return false;
                                            }
                                            return true;
                                        }));
                                        if (!empty($filtered)) {
                                            $filteredHistRounds[$rNum] = $filtered;
                                        }
                                    }
                                @endphp
                                <div class="bracket-wrapper" style="background: rgba(0,0,0,0.15); border: 1px solid var(--border-color); border-radius: 10px; max-width: 100%; overflow-x: auto; padding: 1rem; display: flex; gap: 1.5rem;">
                                    @foreach ($filteredHistRounds as $roundNum => $roundMatches)
                                        <div class="bracket-round" style="display: flex; flex-direction: column; justify-content: space-around; gap: 1rem; min-width: 240px;">
                                            <h5 style="text-align: center; color: var(--text-muted); margin-bottom: 0.5rem; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                @php
                                                    $maxRounds = is_array($this->bracketRounds) ? max(array_keys($this->bracketRounds)) : (is_object($this->bracketRounds) && method_exists($this->bracketRounds, 'keys') ? $this->bracketRounds->keys()->max() : count($this->bracketRounds));
                                                    $stagesLeft = $maxRounds - $roundNum;
                                                    if ($stagesLeft === 0) {
                                                        $roundName = 'Final';
                                                    } elseif ($stagesLeft === 1) {
                                                        $roundName = 'Semifinal';
                                                    } elseif ($stagesLeft === 2) {
                                                        $roundName = 'Perempat Final';
                                                    } else {
                                                        $teamsInRound = pow(2, $stagesLeft + 1);
                                                        $roundName = "Babak {$teamsInRound} Besar";
                                                    }
                                                @endphp
                                                {{ $roundName }}
                                            </h5>
                                            
                                            @php
                                                $pairs = collect($roundMatches)->groupBy(function($m) {
                                                    $parts = explode('.', $m['bracket_position']);
                                                    $pos = end($parts);
                                                    return ceil((int)$pos / 2);
                                                });
                                            @endphp

                                            @foreach ($pairs as $pairGroup)
                                                <div class="bracket-pair" style="display: flex; flex-direction: column; justify-content: space-around; flex: 1; position: relative;">
                                                    
                                                    {{-- Connector Lines --}}
                                                    @if (!$loop->parent->last)
                                                        @if ($pairGroup->count() == 2)
                                                            <div style="position: absolute; right: -0.75rem; top: 25%; bottom: 25%; width: 2px; background: rgba(57, 211, 83, 0.3); z-index: 0;"></div>
                                                            <div style="position: absolute; right: -1.5rem; top: 50%; width: 0.75rem; height: 2px; background: rgba(57, 211, 83, 0.3); z-index: 0;"></div>
                                                        @elseif ($pairGroup->count() == 1)
                                                            <div style="position: absolute; right: -1.5rem; top: 50%; width: 1.5rem; height: 2px; background: rgba(57, 211, 83, 0.3); z-index: 0;"></div>
                                                        @endif
                                                    @endif

                                                    @foreach ($pairGroup as $match)
                                                        @php
                                                            $home = collect($match['participants'])->where('side', 'home')->first();
                                                            $away = collect($match['participants'])->where('side', 'away')->first();
                                                            
                                                            $isHomeMe = $home && in_array($home['tournament_entry_id'], $activeEntryIds);
                                                            $isAwayMe = $away && in_array($away['tournament_entry_id'], $activeEntryIds);
                                                        @endphp
                                                        <div class="bracket-match" style="position: relative; margin: 0.5rem 0; z-index: 1; border: 1px solid {{ ($isHomeMe || $isAwayMe) ? 'var(--primary)' : 'var(--border-color)' }}; box-shadow: {{ ($isHomeMe || $isAwayMe) ? '0 0 8px var(--primary-glow)' : 'none' }}; border-radius: 8px; overflow: hidden; background: var(--bg-surface); width: 220px;">
                                                            {{-- Horizontal line from previous round --}}
                                                            @if (!$loop->parent->parent->first)
                                                                <div style="position: absolute; left: -0.75rem; top: 50%; width: 0.75rem; height: 2px; background: rgba(57, 211, 83, 0.3); z-index: -1;"></div>
                                                            @endif
                                                            {{-- Horizontal line to vertical connector --}}
                                                            @if (!$loop->parent->parent->last && $pairGroup->count() == 2)
                                                                <div style="position: absolute; right: -0.75rem; top: 50%; width: 0.75rem; height: 2px; background: rgba(57, 211, 83, 0.3); z-index: -1;"></div>
                                                            @endif

                                                            <div style="background-color: var(--bg-surface); padding: 0.25rem 0.4rem; font-size: 0.65rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between;">
                                                                <span>Pos: {{ $match['bracket_position'] }}</span>
                                                                @if($match['is_bye'])
                                                                    <span class="badge badge-info" style="font-size:0.55rem; padding: 0 0.2rem;">BYE</span>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="bracket-player {{ $home && $home['is_winner'] ? 'winner' : '' }}" style="border-bottom: 1px solid var(--border-color); font-weight: {{ $isHomeMe ? '800' : '500' }}; background: {{ $isHomeMe ? 'rgba(57, 211, 83, 0.05)' : '' }}; padding: 0.45rem 0.6rem; font-size: 0.78rem; display: flex; justify-content: space-between; align-items: center;">
                                                                <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1; padding-right: 0.4rem;">
                                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                        {{ $home ? ($home['entry']['display_name'] ?? 'TBD') : 'TBD' }}
                                                                    </span>
                                                                    @if ($home && isset($home['club']['name']))
                                                                        <span style="font-size: 0.6rem; color: var(--text-muted); font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">🏟 {{ $home['club']['name'] }}</span>
                                                                    @endif
                                                                </div>
                                                                <span class="bracket-score" style="font-weight: 850; font-size: 0.85rem; color: {{ $home && $home['is_winner'] ? 'var(--primary)' : 'var(--text-muted)' }};">
                                                                    {{ $match['is_bye'] && $home && $home['is_winner'] ? 'BYE' : ($match['status'] === 'walkover' && $home && $home['is_winner'] ? '3' : ($home ? $home['goals_scored'] : '-')) }}@if(!empty($match['decided_by_penalty']))<span style="font-size: 0.85rem; font-weight: 850; color: var(--text-muted); margin-left: 0.35rem;">({{ $match['penalty_score_home'] ?? 0 }})</span>@endif
                                                                </span>
                                                            </div>

                                                            <div class="bracket-player {{ $away && $away['is_winner'] ? 'winner' : '' }}" style="font-weight: {{ $isAwayMe ? '800' : '500' }}; background: {{ $isAwayMe ? 'rgba(57, 211, 83, 0.05)' : '' }}; padding: 0.45rem 0.6rem; font-size: 0.78rem; display: flex; justify-content: space-between; align-items: center;">
                                                                <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1; padding-right: 0.4rem;">
                                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                        {{ $away ? ($away['entry']['display_name'] ?? 'TBD') : 'TBD' }}
                                                                    </span>
                                                                    @if ($away && isset($away['club']['name']))
                                                                        <span style="font-size: 0.6rem; color: var(--text-muted); font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">🏟 {{ $away['club']['name'] }}</span>
                                                                    @endif
                                                                </div>
                                                                <span class="bracket-score" style="font-weight: 850; font-size: 0.85rem; color: {{ $away && $away['is_winner'] ? 'var(--primary)' : 'var(--text-muted)' }};">
                                                                    {{ $match['is_bye'] && $away && $away['is_winner'] ? 'BYE' : ($match['status'] === 'walkover' && $away && $away['is_winner'] ? '3' : ($away ? $away['goals_scored'] : '-')) }}@if(!empty($match['decided_by_penalty']))<span style="font-size: 0.85rem; font-weight: 850; color: var(--text-muted); margin-left: 0.35rem;">({{ $match['penalty_score_away'] ?? 0 }})</span>@endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                @if($thirdPlaceMatchHist)
                                    @php
                                        $home3h = collect($thirdPlaceMatchHist['participants'])->where('side', 'home')->first();
                                        $away3h = collect($thirdPlaceMatchHist['participants'])->where('side', 'away')->first();
                                        $isHome3hMe = $home3h && in_array($home3h['tournament_entry_id'], $activeEntryIds);
                                        $isAway3hMe = $away3h && in_array($away3h['tournament_entry_id'], $activeEntryIds);
                                    @endphp
                                    <div style="margin-top: 1.25rem;">
                                        <h5 style="text-align: center; color: var(--accent); margin-bottom: 0.75rem; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            🏅 {{ app()->getLocale() == 'id' ? 'Perebutan Juara 3' : '3rd Place Match' }}
                                        </h5>
                                        <div class="bracket-match" style="max-width: 240px; margin: 0 auto; border: 1px solid {{ ($isHome3hMe || $isAway3hMe) ? 'var(--primary)' : 'rgba(255, 214, 0, 0.2)' }}; box-shadow: {{ ($isHome3hMe || $isAway3hMe) ? '0 0 8px var(--primary-glow)' : 'none' }}; border-radius: 8px; overflow: hidden; background: var(--bg-surface);">
                                            <div style="background-color: rgba(255, 214, 0, 0.08); padding: 0.25rem 0.6rem; font-size: 0.65rem; color: var(--accent); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                                <span>3rd Place Match</span>
                                                @if($thirdPlaceMatchHist['status'] === 'completed')
                                                    <span style="font-size:0.55rem; padding: 0 0.3rem; background: rgba(57,211,83,0.1); color: var(--primary); border-radius: 4px;">SELESAI</span>
                                                @endif
                                            </div>
                                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.45rem 0.6rem; border-bottom: 1px solid var(--border-color); font-size: 0.78rem; font-weight: {{ $isHome3hMe ? '800' : '500' }}; background: {{ $isHome3hMe ? 'rgba(57, 211, 83, 0.05)' : '' }};">
                                                <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1; padding-right: 0.4rem;">
                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: {{ $isHome3hMe ? 'var(--primary)' : ($home3h && $home3h['is_winner'] ? 'var(--accent)' : 'var(--text-main)') }}; font-weight: {{ $isHome3hMe ? '800' : '600' }};">{{ $home3h ? ($home3h['entry']['display_name'] ?? 'TBD') : 'TBD' }}</span>
                                                    @if ($home3h && isset($home3h['club']['name']))
                                                        <span style="font-size: 0.6rem; color: var(--text-muted); font-weight: 500;">🏟 {{ $home3h['club']['name'] }}</span>
                                                    @endif
                                                </div>
                                                <span style="font-weight: 850; font-size: 0.85rem; color: {{ $home3h && $home3h['is_winner'] ? 'var(--accent)' : 'var(--text-muted)' }};">{{ $home3h ? ($home3h['goals_scored'] ?? '-') : '-' }}@if(!empty($thirdPlaceMatchHist['decided_by_penalty']))<span style="font-size: 0.85rem; font-weight: 850; color: var(--text-muted); margin-left: 0.35rem;">({{ $thirdPlaceMatchHist['penalty_score_home'] ?? 0 }})</span>@endif</span>
                                            </div>
                                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.45rem 0.6rem; font-size: 0.78rem; font-weight: {{ $isAway3hMe ? '800' : '500' }}; background: {{ $isAway3hMe ? 'rgba(57, 211, 83, 0.05)' : '' }};">
                                                <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1; padding-right: 0.4rem;">
                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: {{ $isAway3hMe ? 'var(--primary)' : ($away3h && $away3h['is_winner'] ? 'var(--accent)' : 'var(--text-main)') }}; font-weight: {{ $isAway3hMe ? '800' : '600' }};">{{ $away3h ? ($away3h['entry']['display_name'] ?? 'TBD') : 'TBD' }}</span>
                                                    @if ($away3h && isset($away3h['club']['name']))
                                                        <span style="font-size: 0.6rem; color: var(--text-muted); font-weight: 500;">🏟 {{ $away3h['club']['name'] }}</span>
                                                    @endif
                                                </div>
                                                <span style="font-weight: 850; font-size: 0.85rem; color: {{ $away3h && $away3h['is_winner'] ? 'var(--accent)' : 'var(--text-muted)' }};">{{ $away3h ? ($away3h['goals_scored'] ?? '-') : '-' }}@if(!empty($thirdPlaceMatchHist['decided_by_penalty']))<span style="font-size: 0.85rem; font-weight: 850; color: var(--text-muted); margin-left: 0.35rem;">({{ $thirdPlaceMatchHist['penalty_score_away'] ?? 0 }})</span>@endif</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endif
                @endif
            @endif
        </div>
    </div>

    <!-- SLOT PURCHASE MODAL -->
    @if ($selectedTournamentId)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div class="card" style="max-width: 540px; width: 100%; max-height: 90vh; overflow-y: auto; border: 1px solid var(--border-color); background-color: var(--bg-card); padding: clamp(1rem, 3vw, 2rem); border-radius: 16px;">
                <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">
                    🛒 {{ __('Beli Slot Turnamen') }}
                </h4>

                @if ($purchaseStep === 1)
                    <!-- STEP 1: SELECT QUANTITY -->
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Maks. Slot per Peserta:</span>
                            <strong style="color: var(--text-main);">{{ \App\Models\Tournament::find($selectedTournamentId)?->max_slot_per_player }} Slot</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Sisa Slot Tersedia:</span>
                            <strong style="color: var(--primary);">{{ $remainingOverall }} Slot</strong>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="slot_count">{{ __('Jumlah Slot') }}</label>
                        <input type="number" id="slot_count" wire:model.live.debounce.500ms="slot_count" class="form-control" min="1" required>
                        @error('slot_count') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                    </div>

                    <div style="background: rgba(59, 130, 246, 0.1); border-radius: 8px; padding: 0.75rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <span style="font-weight: 600; font-size: 0.95rem;">{{ __('Total Harga') }}</span>
                        <span style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="button" wire:click="$set('selectedTournamentId', null)" class="btn btn-secondary">Batal</button>
                        <button type="button" wire:click="proceedToPayment" class="btn btn-primary">Lanjut ke Pembayaran</button>
                    </div>
                @else
                    <!-- STEP 2: CHOOSE PAYMENT METHOD & SUBMIT -->
                    <form wire:submit.prevent="submitPurchase">
                        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.25rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Jumlah Slot Dipesan:</span>
                                <strong style="color: var(--text-main);">{{ $slot_count }} Slot</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Total Pembayaran:</span>
                                <strong style="color: var(--primary);">Rp {{ number_format($total_price, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">{{ __('Metode Pembayaran') }}</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <!-- QRIS Tab -->
                                <button type="button" 
                                        wire:click="$set('payment_method', 'qris')"
                                        style="flex: 1; padding: 1rem; border-radius: 12px; border: 2px solid {{ $payment_method === 'qris' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $payment_method === 'qris' ? 'rgba(0, 230, 118, 0.1)' : 'var(--bg-surface)' }}; color: {{ $payment_method === 'qris' ? 'var(--primary)' : 'var(--text-main)' }}; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                                    📱 <span>QRIS</span>
                                </button>
                                <!-- Cash Tab -->
                                <button type="button" 
                                        wire:click="$set('payment_method', 'cash')"
                                        style="flex: 1; padding: 1rem; border-radius: 12px; border: 2px solid {{ $payment_method === 'cash' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $payment_method === 'cash' ? 'rgba(0, 230, 118, 0.1)' : 'var(--bg-surface)' }}; color: {{ $payment_method === 'cash' ? 'var(--primary)' : 'var(--text-main)' }}; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                                    💵 <span>Cash / Tunai</span>
                                </button>
                            </div>
                        </div>

                        @if ($payment_method === 'qris')
                            @if ($payment_info)
                                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.9rem;">
                                    <strong style="color: var(--primary); display: block; margin-bottom: 0.25rem;">💰 Informasi QRIS:</strong>
                                    <p style="white-space: pre-line; margin: 0;">{{ $payment_info }}</p>
                                </div>
                            @endif

                            <div class="form-group" style="margin-bottom: 1.5rem;">
                                <label class="form-label" for="payment_proof">{{ __('Bukti Pembayaran QRIS') }} (Format Gambar, Maks 2MB)</label>
                                <input type="file" id="payment_proof" wire:model="payment_proof" class="form-control" accept="image/*" required>
                                <div wire:loading wire:target="payment_proof" style="color: var(--primary); font-size: 0.85rem; margin-top: 0.25rem;">
                                    ⏳ Mengunggah gambar...
                                </div>
                                @error('payment_proof') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div style="background: rgba(0, 230, 118, 0.05); border: 1px solid var(--primary); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem; line-height: 1.5; color: var(--text-main);">
                                📌 <strong>Instruksi Pembayaran Cash:</strong>
                                <p style="margin: 0.5rem 0 0 0; color: var(--text-muted);">Silakan lakukan pembayaran tunai ke Admin di lokasi. Pendaftaran dan pengisian slot Anda akan segera diverifikasi oleh Admin setelah pembayaran tunai diterima secara langsung.</p>
                            </div>
                        @endif

                        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                            <button type="button" wire:click="$set('purchaseStep', 1)" class="btn btn-secondary">Kembali</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                {{ $payment_method === 'qris' ? 'Kirim Bukti QRIS' : 'Ajukan Pendaftaran (Cash)' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- DISPUTE MODAL -->
    @if ($selectedMatchId)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div class="card" style="max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto; border: 1px solid var(--border-color); background-color: var(--bg-card); padding: clamp(1rem, 3vw, 2rem); border-radius: 16px;">
                <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">
                    ⚠️ {{ __('Raise Dispute') }}
                </h4>

                <form wire:submit.prevent="submitDispute">
                    <div class="form-group">
                        <label class="form-label">{{ __('Dispute Reason') }}</label>
                        <textarea class="form-control" wire:model.defer="disputeReason" rows="5" placeholder="Sebutkan detail ketidaksesuaian skor..." required></textarea>
                        @error('disputeReason') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="button" wire:click="$set('selectedMatchId', null)" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background: var(--danger); box-shadow: none;">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- TOAST ALERT -->
    @if ($toastMessage)
        <div
            x-data="{ timer: null }"
            x-init="
                if (timer) clearTimeout(timer);
                timer = setTimeout(() => { $wire.dismissToast(); }, 5000);
            "
            x-effect="
                if (timer) clearTimeout(timer);
                if ($wire.toastMessage) {
                    timer = setTimeout(() => { $wire.dismissToast(); }, 5000);
                }
            "
            class="toast-alert"
            style="position: fixed; bottom: 2rem; right: 2rem; padding: 1rem 1.5rem; border-radius: 8px; font-weight: 600; box-shadow: 0 10px 15px rgba(0,0,0,0.3); z-index: 9999; max-width: calc(100vw - 2rem); cursor: pointer;"
            :style="$wire.toastType === 'error' ? 'background: var(--danger); color: white;' : 'background: var(--primary); color: #000;'"
            wire:click="dismissToast"
        >
            {{ $toastType === 'error' ? '❌' : '✅' }} {{ $toastMessage }}
        </div>
    @endif

    <!-- PAYMENT HISTORY DRAWER (Right Pop-up) -->
    <div id="paymentDrawerBackdrop" class="payment-drawer-backdrop" onclick="togglePaymentDrawer(false)"></div>
    <div id="paymentDrawer" class="payment-drawer" wire:ignore>
        <div class="payment-drawer-header">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--primary);">
                💳 {{ app()->getLocale() == 'id' ? 'Status Pembayaran Slot' : 'Payment Status History' }}
            </h3>
            <button class="payment-drawer-close" onclick="togglePaymentDrawer(false)">&times;</button>
        </div>
        
        <div class="payment-drawer-content">
            @forelse ($pendingPayments as $batch)
                <div class="payment-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; gap: 0.5rem;">
                        <span style="font-weight: 700; font-size: 1rem; color: var(--text-main); line-height: 1.2;">
                            {{ $batch->tournament->name }}
                        </span>
                        @if ($batch->status === 'verified')
                            <span class="badge badge-success" style="font-size: 0.75rem;">VERIFIED</span>
                        @elseif ($batch->status === 'pending')
                            <span class="badge badge-warning" style="font-size: 0.75rem;">PENDING</span>
                        @else
                            <span class="badge badge-danger" style="font-size: 0.75rem;">REJECTED</span>
                        @endif
                    </div>
                    
                    <div style="font-size: 0.88rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>{{ app()->getLocale() == 'id' ? 'Jumlah Slot' : 'Slot Count' }}:</span>
                            <strong style="color: var(--text-main);">{{ $batch->slot_count }} Slot</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>{{ __('Total Harga') }}:</span>
                            <strong style="color: var(--primary);">Rp {{ number_format($batch->total_price, 0, ',', '.') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>{{ app()->getLocale() == 'id' ? 'Metode' : 'Method' }}:</span>
                            <strong style="color: var(--text-main); text-transform: uppercase;">{{ $batch->payment_method }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>{{ app()->getLocale() == 'id' ? 'Tanggal' : 'Date' }}:</span>
                            <strong>{{ $batch->created_at->format('d M Y H:i') }} WIB</strong>
                        </div>
                        @if ($batch->status === 'rejected' && $batch->rejection_reason)
                            <div style="margin-top: 0.5rem; padding: 0.5rem; background: rgba(211, 47, 47, 0.05); border-left: 3px solid var(--danger); border-radius: 4px; color: var(--danger); font-size: 0.82rem;">
                                <strong>{{ app()->getLocale() == 'id' ? 'Alasan Penolakan:' : 'Rejection Reason:' }}</strong>
                                <div style="margin-top: 0.15rem; word-break: break-word;">{{ $batch->rejection_reason }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); margin-top: 4rem;">
                    {{ app()->getLocale() == 'id' ? 'Belum ada riwayat transaksi.' : 'No transaction history found.' }}
                </div>
            @endforelse
        </div>
    </div>

</div>
