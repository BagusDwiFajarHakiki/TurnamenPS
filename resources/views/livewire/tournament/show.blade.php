<div wire:poll.3s="refreshData">
    @if (!$tournament)
        <div class="container" style="text-align: center; padding: 6rem 0;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🏆</div>
            <h2>{{ __('Belum Ada Turnamen') }}</h2>
            <p style="color: var(--text-muted); margin-top: 1rem;">Silakan buat turnamen di admin panel terlebih dahulu.</p>
        </div>
    @else
        <!-- Hero Section -->
        <section class="hero" style="padding: 4rem 1rem 3rem;">
            <div class="container" style="position: relative; z-index: 2;">
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(57, 211, 83, 0.1); border: 1px solid rgba(57, 211, 83, 0.2); padding: 0.35rem 1rem; border-radius: 20px; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">
                    <span class="live-dot-pulse"></span>
                    {{ $tournament->status }}
                </div>
                <h1 style="font-size: 3.5rem; font-weight: 900; letter-spacing: -1.5px; margin-bottom: 0.75rem; line-height: 1.1;">
                    <span class="gradient-text" style="background: linear-gradient(135deg, var(--primary) 0%, #00ffcc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $tournament->name }}</span>
                </h1>
                <p style="font-size: 1.1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto 2rem; font-weight: 500;">
                    🎮 {{ $tournament->game_title }}
                </p>

                @if ($tournament->status === 'registration')
                    <a href="/register-player" wire:navigate class="btn btn-primary" style="font-size: 1rem; padding: 0.8rem 2.25rem; border-radius: 30px; letter-spacing: 0.5px; box-shadow: 0 4px 20px var(--primary-glow);">
                        🚀 {{ __('Daftar Sekarang') }}
                    </a>
                @endif
            </div>
        </section>

        <div class="container">
            <!-- Rules and payment information -->
            <div class="grid grid-cols-2" style="margin-bottom: 3rem; gap: 1.75rem;">
                <!-- Tournament Rules -->
                <div class="glass-card">
                    <h3 class="card-title" style="color: var(--primary); font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        📜 {{ __('Tournament Rules') }}
                    </h3>
                    <div class="soft-well" style="color: var(--text-muted); max-height: 180px; overflow-y: auto; font-size: 0.9rem; line-height: 1.6; border-radius: 8px;">
                        {!! $tournament->rules_content ?: 'Peraturan standard eFootball / PES.' !!}
                    </div>
                </div>

                <!-- Registration Tracker -->
                <div class="glass-card">
                    <h3 class="card-title" style="color: var(--secondary); font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        📊 {{ __('Registration Status') }}
                    </h3>
                    @php
                        $verifiedCount = $tournament->entries()->count();
                        $pendingOverall = \App\Models\EntryBatch::where('tournament_id', $tournament->id)->where('status', 'pending')->sum('slot_count');
                        $availSlots = max(0, $tournament->max_entries - ($verifiedCount + $pendingOverall));
                        $filledPercent = $tournament->max_entries > 0 ? min(100, round(($verifiedCount / $tournament->max_entries) * 100)) : 0;
                    @endphp
                    
                    <div style="margin-bottom: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.35rem;">
                            <span>Kuota Terisi</span>
                            <span style="color: var(--text-main);">{{ $verifiedCount }} / {{ $tournament->max_entries }} Slot ({{ $filledPercent }}%)</span>
                        </div>
                        <div class="modern-progress-container">
                            <div class="modern-progress-bar" style="width: {{ $filledPercent }}%"></div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <div class="minimal-list-item">
                            <span style="color: var(--text-muted); font-size: 0.9rem;">Harga per Slot</span>
                            <span style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">Rp {{ number_format($tournament->price_per_slot, 0, ',', '.') }}</span>
                        </div>
                        <div class="minimal-list-item">
                            <span style="color: var(--text-muted); font-size: 0.9rem;">Maksimal Slot per Pemain</span>
                            <span style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $tournament->max_slot_per_player }}</span>
                        </div>
                        <div class="minimal-list-item">
                            <span style="color: var(--text-muted); font-size: 0.9rem;">Slot Tersedia</span>
                            <span style="font-weight: 700; color: var(--primary); font-size: 0.95rem;">{{ $availSlots }} Slot</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Stats & Standings (3 Columns) -->
            <div class="grid grid-cols-3" style="margin-bottom: 3rem; gap: 1.75rem; display: grid;">
                <!-- Column 1: BAGAN LIVE -->
                <div class="glass-card" style="display: flex; flex-direction: column; justify-content: flex-start;">
                    <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: lowercase; margin-bottom: 0.5rem;">
                        <span class="live-dot-pulse"></span>
                        langsung
                    </div>
                    <h3 class="card-title" style="margin-bottom: 0.5rem; text-transform: uppercase; font-size: 1.15rem; font-weight: 800;">BAGAN LIVE</h3>
                    <p style="color: var(--text-muted); font-size: 0.825rem; line-height: 1.4; margin-bottom: 1rem; flex-grow: 0;">
                        Bracket pertandingan diperbarui secara real-time. Lihat siapa melawan siapa di setiap babak, hasil masuk, dan pertandingan selanjutnya.
                    </p>
                    <div class="soft-well" style="max-height: 160px; overflow-y: auto; flex-grow: 1; padding: 0.75rem 1rem;">
                        @forelse($baganLiveMatches as $match)
                            @php
                                $homePart = collect($match['participants'])->where('side', 'home')->first();
                                $awayPart = collect($match['participants'])->where('side', 'away')->first();
                                $homeLabel = $homePart ? ($homePart['entry']['display_name'] ?? 'TBD') : 'TBD';
                                $awayLabel = $awayPart ? ($awayPart['entry']['display_name'] ?? 'TBD') : 'TBD';
                                
                                $roundName = "Round {$match['round_number']}";
                                if (($match['bracket_position'] ?? '') === '3rd_place') {
                                    $roundName = app()->getLocale() == 'id' ? 'Juara 3' : '3rd Place';
                                } elseif ($match['round_number'] == $maxRoundNumber && $maxRoundNumber > 1) {
                                    $roundName = "Final";
                                }
                            @endphp
                            <div class="minimal-list-item" style="font-size: 0.85rem;">
                                <span>[ {{ $roundName }} ] {{ $homeLabel }} vs {{ $awayLabel }}</span>
                                @if(in_array($match['status'], ['completed', 'walkover']))
                                    <strong style="color: var(--primary);">{{ $homePart['goals_scored'] }} - {{ $awayPart['goals_scored'] }}</strong>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">vs</span>
                                @endif
                            </div>
                        @empty
                            <div style="color: var(--text-muted); text-align: center; padding: 1rem 0;">
                                Belum ada pertandingan.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Column 2: TOP SKOR -->
                <div class="glass-card" style="display: flex; flex-direction: column; justify-content: flex-start;">
                    <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: lowercase; margin-bottom: 0.5rem;">
                        <span class="live-dot-pulse"></span>
                        langsung
                    </div>
                    <h3 class="card-title" style="margin-bottom: 0.5rem; text-transform: uppercase; font-size: 1.15rem; font-weight: 800;">TOP SKOR</h3>
                    <p style="color: var(--text-muted); font-size: 0.825rem; line-height: 1.4; margin-bottom: 1rem; flex-grow: 0;">
                        Akumulasi gol seluruh pemain — dihitung dari semua slot yang dimiliki. Pemain yang sudah gugur tetap tampil sebagai penghargaan performa.
                    </p>
                    <div class="soft-well" style="max-height: 160px; overflow-y: auto; flex-grow: 1; padding: 0.75rem 1rem;">
                        @forelse($topScorers as $index => $row)
                            <div class="minimal-list-item" style="font-size: 0.85rem;">
                                <span>{{ $index + 1 }}. {{ $row->player->name }} <span style="font-size: 0.75rem; color: var(--text-muted);">({{ $row->total_entries }} slot)</span></span>
                                <span style="font-weight: 700; color: var(--text-main);">
                                    {{ $row->total_goals_scored }} gol
                                    @if($row->active_entries_count > 0)
                                        🔥
                                    @endif
                                </span>
                            </div>
                        @empty
                            <div style="color: var(--text-muted); text-align: center; padding: 1rem 0;">
                                Belum ada data top skor.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Column 3: RANKING KLUB TERPOPULER -->
                <div class="glass-card" style="display: flex; flex-direction: column; justify-content: flex-start;">
                    <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: lowercase; margin-bottom: 0.5rem;">
                        <span class="live-dot-pulse"></span>
                        langsung
                    </div>
                    <h3 class="card-title" style="margin-bottom: 0.5rem; text-transform: uppercase; font-size: 1.15rem; font-weight: 800;">RANKING KLUB</h3>
                    <p style="color: var(--text-muted); font-size: 0.825rem; line-height: 1.4; margin-bottom: 1rem; flex-grow: 0;">
                        Klub/tim paling sering dipakai dari gabungan semua turnamen. Dihitung otomatis dari seluruh pertandingan tercatat untuk melihat tren meta game.
                    </p>
                    <div class="soft-well" style="max-height: 160px; overflow-y: auto; flex-grow: 1; padding: 0.75rem 1rem;">
                        @forelse($popularClubsCombined as $index => $row)
                            @if($index === 0)
                                <div class="minimal-list-item" style="font-size: 0.85rem;">
                                    <span>1. {{ $row->club->name ?? 'Unknown' }}</span>
                                    <strong style="color: var(--secondary);">{{ $row->usage_count }}x</strong>
                                </div>
                            @elseif($index === 1)
                                <div class="minimal-list-item" style="font-size: 0.85rem;">
                                    <div style="display: flex; gap: 1rem; width: 100%; justify-content: space-between;">
                                        <span>
                                            2. {{ $row->club->name ?? 'Unknown' }} (<strong style="color: var(--secondary);">{{ $row->usage_count }}x</strong>)
                                            @if(isset($popularClubsCombined[2]))
                                                &nbsp;&nbsp;·&nbsp;&nbsp;
                                                3. {{ $popularClubsCombined[2]->club->name ?? 'Unknown' }} (<strong style="color: var(--secondary);">{{ $popularClubsCombined[2]->usage_count }}x</strong>)
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @elseif($index > 2)
                                <div class="minimal-list-item" style="font-size: 0.85rem;">
                                    <span>{{ $index + 1 }}. {{ $row->club->name ?? 'Unknown' }}</span>
                                    <strong>{{ $row->usage_count }}x</strong>
                                </div>
                            @endif
                        @empty
                            <div style="color: var(--text-muted); text-align: center; padding: 1rem 0;">
                                Belum ada data klub.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tab Buttons for visual bracket, leaderboard, schedules -->
            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.75rem; font-weight: 900; margin-bottom: 1.5rem; letter-spacing: -0.5px;">
                    <span class="gradient-text">{{ __('Tournament Dashboard') }}</span>
                </h3>
            </div>

            <!-- Main Tournament Dashboard Tabs -->
            <div class="glass-card" style="padding: 2rem;">
                <!-- Tab Headers (Segmented Control) -->
                <div style="margin-bottom: 2rem; overflow-x: auto;">
                    <div class="segmented-control">
                        @foreach ($stages as $stage)
                            <button wire:click="selectStage({{ $stage->id }})" 
                                    class="segmented-item {{ $activeStageId == $stage->id ? 'active' : '' }}">
                                {{ $stage->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Visual Seeding and Tree Bracket -->
                @if ($activeStageId)
                    <div style="margin-bottom: 4rem;">
                        <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--primary);">
                            🌳 {{ __('Tournament Bracket') }}
                        </h4>
                        
                        @if (empty($rounds))
                            <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">
                                {{ app()->getLocale() == 'id' ? 'Bagan pertandingan belum digenerate.' : 'The tournament bracket has not been generated yet.' }}
                            </p>
                        @else
                            @include('_partials.bracket-tree', ['bracketRounds' => $rounds])
                        @endif
                    </div>
                @endif

                <!-- Leaderboard & Match schedules -->
                <div class="grid grid-cols-2" style="gap: 2rem;">
                    <!-- Leaderboard -->
                    <div>
                        <h4 style="margin-bottom: 1.5rem; font-weight: 800; color: var(--accent); font-size: 1.1rem;">
                            📊 {{ __('Leaderboard & Top Scorer') }}
                        </h4>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 60px; text-align: center;">{{ __('Rank') }}</th>
                                        <th>{{ __('Player') }}</th>
                                        <th style="text-align: center;">{{ __('Matches') }}</th>
                                        <th style="text-align: center;">{{ __('Wins') }}</th>
                                        <th style="text-align: center;">{{ __('Goals') }}</th>
                                        <th style="text-align: center;">{{ __('Streak') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($leaderboard as $index => $row)
                                        <tr>
                                            <td style="text-align: center; font-weight: 800; font-size: 1rem;">
                                                @if($index === 0)
                                                    🥇
                                                @elseif($index === 1)
                                                    🥈
                                                @elseif($index === 2)
                                                    🥉
                                                @else
                                                    <span style="color: var(--text-muted);">#{{ $row->rank_position ?? ($index + 1) }}</span>
                                                @endif
                                            </td>
                                            <td style="font-weight: 700;">{{ $row->player->name }}</td>
                                            <td style="text-align: center;">{{ $row->total_matches_played }}</td>
                                            <td style="text-align: center;">{{ $row->total_wins }}</td>
                                            <td style="text-align: center; color: var(--primary); font-weight: 700;">{{ $row->total_goals_scored }}</td>
                                            <td style="text-align: center;">
                                                <span style="font-size: 0.8rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 6px; background: rgba(57,211,83,0.1); color: var(--primary);">
                                                    🔥 {{ $row->current_win_streak }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align: center; color: var(--text-muted);">
                                                {{ __('Belum ada statistik pertandingan.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Match Schedules -->
                    <div>
                        <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--secondary);">
                            ⚔️ {{ __('Console Queue') }} & {{ __('Ongoing Matches') }}
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                            
                            @forelse ($upcomingMatches as $match)
                                @php
                                    $homePart = $match->participants->where('side', 'home')->first();
                                    $awayPart = $match->participants->where('side', 'away')->first();
                                    $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                    $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                                @endphp
                                <div class="soft-well" style="padding: 1rem 1.25rem; border-left: 3px solid {{ $match->status === 'ongoing' ? 'var(--primary)' : 'var(--border-color)' }}; border-radius: 0 12px 12px 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.2rem 0.6rem; border-radius: 6px; background: {{ $match->status === 'ongoing' ? 'rgba(57,211,83,0.15)' : 'rgba(255,193,7,0.15)' }}; color: {{ $match->status === 'ongoing' ? 'var(--primary)' : '#FFC107' }};">
                                            {{ strtoupper($match->status) }}
                                        </span>
                                        @if ($match->psUnit)
                                            <span style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">
                                                🎮 {{ $match->psUnit->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <div style="display: flex; align-items: center; font-weight: 700; font-size: 0.95rem;">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $homeName }}</div>
                                            @if($homePart?->club?->name)
                                                <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $homePart->club->name }}</div>
                                            @endif
                                        </div>
                                        <div style="flex-shrink: 0; text-align: center; padding: 0 0.75rem; min-width: 70px;">
                                            @if($match->status === 'ongoing' || $match->status === 'completed' || $match->status === 'walkover')
                                                <div style="font-size: 1.1rem; letter-spacing: 2px; font-weight: 800; color: var(--primary);">{{ $homePart?->goals_scored ?? 0 }} - {{ $awayPart?->goals_scored ?? 0 }}</div>
                                                @if($match->decided_by_penalty)
                                                    <div style="font-size: 0.65rem; letter-spacing: 1px; color: var(--text-muted); margin-top: 0.15rem;">({{ $match->penalty_score_home }}) - ({{ $match->penalty_score_away }})</div>
                                                @endif
                                            @else
                                                <span style="color: var(--text-muted); font-size: 0.8rem; font-weight: 500;">VS</span>
                                            @endif
                                        </div>
                                        <div style="flex: 1; min-width: 0; text-align: right;">
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $awayName }}</div>
                                            @if($awayPart?->club?->name)
                                                <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $awayPart->club->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div style="text-align: center; padding: 2rem 0; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; font-size: 0.9rem;">
                                    {{ __('Tidak ada antrean match aktif.') }}
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
