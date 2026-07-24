<div style="margin-top: 2rem;">
    <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
        Recap Turnamen
    </h4>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <!-- BLOCK 01 - PODIUM -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1rem; border-top: 4px solid #10b981;">
            <div>
                <h3 class="text-gray-900 dark:text-white" style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem;">
                    JUARA
                </h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem;">
                @php
                    $juara1 = $tournament->getJuara(1);
                    $juara2 = $tournament->getJuara(2);
                    $juara3 = $tournament->getJuara(3);
                @endphp
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

        <!-- BLOCK 02 - TOP SKOR -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1rem; border-top: 4px solid #3b82f6;">
            <div>
                <h3 class="text-gray-900 dark:text-white" style="font-size: 1.25rem; font-weight: 800; margin-top: 0.25rem;">
                    TOP SKOR
                </h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.5rem;">
                @php
                    $topScorers = $tournament->getTopScorers();
                @endphp
                @forelse($topScorers as $idx => $ts)
                    <div class="text-gray-900 dark:text-white" style="display: flex; justify-content: space-between; font-size: 0.9rem; padding: 0.35rem 0; border-bottom: 1px solid rgba(156, 163, 175, 0.2);">
                        <span>{{ $idx + 1 }}. <strong>{{ $ts->player->name }}</strong></span>
                        <strong class="text-blue-500">{{ $ts->total_goals_scored }} Gol</strong>
                    </div>
                @empty
                    <div class="text-gray-500 dark:text-gray-400" style="font-size: 0.85rem; text-align: center; margin-top: 1rem;">Belum ada pencetak gol.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
