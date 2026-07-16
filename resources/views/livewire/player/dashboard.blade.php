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

                        {{-- Visual Seeding and Tree Bracket --}}
                        <div style="margin-bottom: 4rem;">
                            <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--primary);">
                                🌳 {{ __('Tournament Bracket') }}
                            </h4>
                            
                            @if (empty($this->bracketRounds) || count($this->bracketRounds) === 0)
                                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); font-size: 0.88rem; border: 1px dashed var(--border-color); border-radius: 12px; background: var(--bg-surface);">
                                    🌳 Bagan pertandingan untuk turnamen ini belum digenerate oleh admin.
                                </div>
                            @else
                                @include('_partials.bracket-tree', ['bracketRounds' => $this->bracketRounds, 'activeEntryIds' => $this->activeEntryIds])
                            @endif
                        </div>

                        {{-- Jadwal & Riwayat Pertandingan Anda --}}
                        <style>
                            @media (min-width: 768px) {
                                .md-grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                            }
                        </style>
                        <div class="grid grid-cols-1 md-grid-cols-2" style="margin-top: 1.5rem; gap: 2rem; display: grid;">
                            <!-- Jadwal & Sedang Berjalan -->
                            <div>
                                <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--secondary); font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    ⚔️ Jadwal & Sedang Berjalan
                                </h4>
                                <div style="display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                                    @php
                                        $upcomingMatches = collect($this->bracketMyMatches)->filter(fn($m) => !in_array($m->status, ['completed', 'walkover']));
                                    @endphp
                                    @forelse ($upcomingMatches as $match)
                                        @php
                                            $homePart = $match->participants->where('side', 'home')->first();
                                            $awayPart = $match->participants->where('side', 'away')->first();
                                            $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                            $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                                            $isHomeMe = in_array($homePart?->tournament_entry_id, $this->activeEntryIds);
                                            
                                        @endphp
                                        <div class="soft-well" style="padding: 1rem 1.25rem; border-left: 3px solid {{ $match->status === 'ongoing' ? 'var(--primary)' : 'var(--border-color)' }}; border-radius: 12px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                                <div style="flex: 1;">
                                                    <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.2rem 0.6rem; border-radius: 6px; background: {{ $match->status === 'ongoing' ? 'rgba(57,211,83,0.15)' : 'rgba(255,193,7,0.15)' }}; color: {{ $match->status === 'ongoing' ? 'var(--primary)' : '#FFC107' }};">
                                                        {{ strtoupper($match->status) }}
                                                    </span>
                                                </div>
                                                <div style="flex: 1; text-align: center;">
                                                    <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.2rem 0.6rem; border-radius: 6px; background: var(--bg-surface); color: var(--text-muted);">
                                                        {{ $match->computedRoundName ?? $match->stage?->name }}
                                                    </span>
                                                </div>
                                                <div style="flex: 1; text-align: right;">
                                                    @if ($match->psUnit)
                                                        <span style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">
                                                            🎮 {{ $match->psUnit->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; font-weight: 700; font-size: 1.05rem;">
                                                <div style="flex: 1; min-width: 0;">
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: {{ $isHomeMe ? 'var(--primary)' : 'inherit' }}; font-weight: {{ $isHomeMe ? '800' : '700' }};">{{ $homeName }}</div>
                                                    @if($homePart?->club?->name)
                                                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $homePart->club->name }}</div>
                                                    @endif
                                                </div>
                                                <div style="flex-shrink: 0; text-align: center; padding: 0 1rem; min-width: 80px;">
                                                    @if($match->status === 'ongoing')
                                                        <div style="font-size: 1.25rem; letter-spacing: 2px; font-weight: 800; color: var(--primary);">{{ $homePart?->goals_scored ?? 0 }} - {{ $awayPart?->goals_scored ?? 0 }}</div>
                                                    @else
                                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">VS</span>
                                                    @endif
                                                </div>
                                                <div style="flex: 1; min-width: 0; text-align: right;">
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: {{ !$isHomeMe ? 'var(--primary)' : 'inherit' }}; font-weight: {{ !$isHomeMe ? '800' : '700' }};">{{ $awayName }}</div>
                                                    @if($awayPart?->club?->name)
                                                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $awayPart->club->name }}</div>
                                                    @endif
                                                </div>
                                            </div>


                                        </div>
                                    @empty
                                        <div style="text-align: center; padding: 2.5rem 1.5rem; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface);">
                                            Belum ada jadwal pertandingan.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Pertandingan Selesai -->
                            <div>
                                <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--text-muted); font-size: 1.1rem;">
                                    ✅ Pertandingan Selesai
                                </h4>
                                <div style="display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                                    @php
                                        $completedMatches = collect($this->bracketMyMatches)->filter(fn($m) => in_array($m->status, ['completed', 'walkover']));
                                    @endphp
                                    @forelse ($completedMatches as $match)
                                        @php
                                            $homePart = $match->participants->where('side', 'home')->first();
                                            $awayPart = $match->participants->where('side', 'away')->first();
                                            $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                            $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                                            $homeScore = $match->status === 'walkover' && $homePart && $homePart->is_winner ? '3' : ($homePart ? $homePart->goals_scored : '-');
                                            $awayScore = $match->status === 'walkover' && $awayPart && $awayPart->is_winner ? '3' : ($awayPart ? $awayPart->goals_scored : '-');
                                            $roundName = $match->computedRoundName ?? $match->stage?->name ?? 'Babak';
                                            $isHomeMe = in_array($homePart?->tournament_entry_id, $this->activeEntryIds);
                                        @endphp
                                        <div class="soft-well" style="padding: 1rem 1.25rem; border-left: 3px solid var(--border-color); border-radius: 12px; opacity: 0.8;">
                                            <div style="text-align: center; margin-bottom: 0.5rem;">
                                                <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.2rem 0.6rem; border-radius: 6px; background: var(--bg-surface); color: var(--text-muted);">
                                                    {{ $roundName }}
                                                </span>
                                            </div>
                                            <div style="display: flex; align-items: center; font-weight: 700; font-size: 1.05rem;">
                                                <div style="flex: 1; min-width: 0;">
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: {{ ($homePart && $homePart->is_winner) || $isHomeMe ? 'var(--primary)' : 'inherit' }}; font-weight: {{ $isHomeMe ? '800' : '700' }};">{{ $homeName }}</div>
                                                    @if($homePart?->club?->name)
                                                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $homePart->club->name }}</div>
                                                    @endif
                                                </div>
                                                <div style="flex-shrink: 0; text-align: center; padding: 0 1rem; min-width: 80px;">
                                                    <div style="font-size: 1.25rem; letter-spacing: 2px;">{{ $homeScore }} - {{ $awayScore }}</div>
                                                    @if($match->decided_by_penalty)
                                                        <div style="font-size: 0.7rem; letter-spacing: 1px; color: var(--text-muted);">({{ $match->penalty_score_home }}) - ({{ $match->penalty_score_away }})</div>
                                                    @endif
                                                </div>
                                                <div style="flex: 1; min-width: 0; text-align: right;">
                                                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: {{ ($awayPart && $awayPart->is_winner) || !$isHomeMe ? 'var(--primary)' : 'inherit' }}; font-weight: {{ !$isHomeMe ? '800' : '700' }};">{{ $awayName }}</div>
                                                    @if($awayPart?->club?->name)
                                                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $awayPart->club->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div style="text-align: center; padding: 2.5rem 1.5rem; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface);">
                                            Belum ada pertandingan selesai.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endif
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
