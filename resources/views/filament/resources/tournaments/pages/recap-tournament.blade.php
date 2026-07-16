<x-filament-panels::page wire:poll.3s="calculateRecap">
    <!-- TOP SECTION: 3-COLUMN GRID -->
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <!-- BLOCK 01 - PODIUM -->
        <div class="fi-ta-record-card rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" style="display: flex; flex-direction: column; gap: 1rem; border-top: 4px solid #10b981;">
            <div>
                <span class="text-emerald-500" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">01 - PODIUM</span>
                <h3 class="text-gray-900 dark:text-white" style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem;">
                    JUARA 1, 2 & 3
                </h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">🥇</span>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.75rem; font-weight: 600;">JUARA 1</div>
                        <strong class="text-emerald-500" style="font-size: 1.1rem;">{{ $juara1 ?: 'Belum ditentukan' }}</strong>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">🥈</span>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.75rem; font-weight: 600;">JUARA 2</div>
                        <strong class="text-gray-900 dark:text-white" style="font-size: 1.1rem;">{{ $juara2 ?: 'Belum ditentukan' }}</strong>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">🥉</span>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.75rem; font-weight: 600;">JUARA 3</div>
                        @if($juara3)
                            <strong class="text-gray-900 dark:text-white">{{ $juara3 }}</strong>
                        @else
                            <strong class="text-gray-400 dark:text-gray-500">Belum ditentukan</strong>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOCK 02 - STATISTIK -->
        <div class="fi-ta-record-card rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" style="display: flex; flex-direction: column; gap: 1rem; border-top: 4px solid #3b82f6;">
            <div>
                <span class="text-blue-500" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">02 - STATISTIK</span>
                <h3 class="text-gray-900 dark:text-white" style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem;">
                    TOP SKOR & WIN STREAK
                </h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 0.5rem;">
                <div>
                    <h4 class="text-gray-500 dark:text-gray-400" style="font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                        ⚽ TOP SKORER
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        @forelse($topScorers as $idx => $ts)
                            <div class="text-gray-900 dark:text-white" style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                <span>{{ $idx + 1 }}. <strong>{{ $ts->player->name }} {{ $playerEntryNumbers[$ts->player_id] ?? '' }}</strong></span>
                                <strong class="text-emerald-500">{{ $ts->total_goals_scored }} Gol</strong>
                            </div>
                        @empty
                            <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.85rem;">Belum ada data gol.</div>
                        @endforelse
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700" style="padding-top: 0.75rem;">
                    <h4 class="text-gray-500 dark:text-gray-400" style="font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                        🔥 WIN STREAK TERPANJANG
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        @forelse($topStreaks as $idx => $tst)
                            <div class="text-gray-900 dark:text-white" style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                <span>{{ $idx + 1 }}. <strong>{{ $tst->player->name }} {{ $playerEntryNumbers[$tst->player_id] ?? '' }}</strong></span>
                                <strong class="text-blue-500">🔥 {{ $tst->best_win_streak }} Match</strong>
                            </div>
                        @empty
                            <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.85rem;">Belum ada data win streak.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOCK 03 - KLUB -->
        <div class="fi-ta-record-card rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" style="display: flex; flex-direction: column; gap: 1rem; border-top: 4px solid #a855f7;">
            <div>
                <span class="text-purple-500" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">03 - KLUB</span>
                <h3 class="text-gray-900 dark:text-white" style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem;">
                    RANKING KLUB TERPOPULER
                </h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                @forelse($popularClubs as $idx => $pc)
                    <div class="border-b border-gray-100 dark:border-gray-700/50" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="text-purple-500" style="font-weight: 700;">#{{ $idx + 1 }}</span>
                            <span class="text-gray-900 dark:text-white" style="font-weight: 600;">{{ $pc->club->name }}</span>
                        </div>
                        <span class="badge badge-info" style="font-size: 0.8rem; padding: 0.2rem 0.5rem; border-radius: 12px; background: rgba(168, 85, 247, 0.1); color: #a855f7; border: 1px solid rgba(168, 85, 247, 0.2);">
                            {{ $pc->usage_count }} Kali
                        </span>
                    </div>
                @empty
                    <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.85rem; text-align: center; margin-top: 2rem;">
                        Belum ada pertandingan tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- MIDDLE SECTION: ADMIN (RINGKASAN ADMINISTRATIF) - FULL WIDTH -->
    <div class="fi-ta-record-card rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" style="display: flex; flex-direction: column; gap: 1rem; border-top: 4px solid #f97316; margin-bottom: 1.5rem;">
        <div>
            <span class="text-orange-500" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">06 - ADMIN</span>
            <h3 class="text-gray-900 dark:text-white" style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem;">
                RINGKASAN ADMINISTRATIF
            </h3>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 0.5rem;">
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50">
                <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.8rem; font-weight: 600;">TOTAL PESERTA</div>
                <div class="text-blue-500" style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">
                    {{ $adminSummary['total_players'] ?? 0 }}
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50">
                <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.8rem; font-weight: 600;">DISPUTE MASUK</div>
                <div class="text-red-500" style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">
                    {{ $adminSummary['disputes_count'] ?? 0 }}
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50">
                <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.8rem; font-weight: 600;">WALKOVER (WO)</div>
                <div class="text-amber-500" style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">
                    {{ $adminSummary['wo_count'] ?? 0 }}
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50">
                <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.8rem; font-weight: 600;">ESTIMASI PENDAPATAN</div>
                <div class="text-emerald-500" style="font-size: 1.75rem; font-weight: 800; margin-top: 0.25rem;">
                    Rp {{ number_format($adminSummary['estimated_revenue'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM SECTION: BAGAN (BAGAN FINAL LENGKAP) - FULL WIDTH -->
    <div class="fi-ta-record-card rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900" style="display: flex; flex-direction: column; gap: 1rem; border-top: 4px solid #14b8a6; margin-bottom: 2rem;">
        <div>
            <span class="text-teal-500" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">04 - BAGAN</span>
            <h3 class="text-gray-900 dark:text-white" style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem;">
                BAGAN FINAL LENGKAP
            </h3>
        </div>
        
        <div style="margin-top: 0.5rem;">
            @if(empty($bracketRounds))
                <div class="text-gray-500 dark:text-gray-400" style="text-align: center; padding: 2rem;">
                    Bagan belum dibuat atau tidak tersedia untuk turnamen ini.
                </div>
            @else
                @include('_partials.bracket-tree', ['bracketRounds' => $bracketRounds, 'activeEntryIds' => [], 'centerCompact' => false])
            @endif
        </div>
    </div>
</x-filament-panels::page>
