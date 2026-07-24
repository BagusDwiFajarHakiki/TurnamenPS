<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <style>

        .turnamen-recap-admin {
            --bg-card:    #0F1A0F;
            --primary:        #39D353;
            --secondary:      #00C2FF;
            --text-main:  #E8F5E9;
            --text-muted: #6B8F6B;
            --border-color:       rgba(57, 211, 83, 0.15);
            /* Scrollbar rules for internal container */
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) rgba(0,0,0,0.1);
        }
        .turnamen-recap-admin::-webkit-scrollbar { width: 8px; height: 8px; }
        .turnamen-recap-admin::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); border-radius: 4px; }
        .turnamen-recap-admin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .turnamen-recap-admin::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        .turnamen-recap-admin .glass-card {
            background: rgba(15, 26, 15, 0.45);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(57, 211, 83, 0.12);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
            position: relative;
            overflow: hidden;
        }
        .turnamen-recap-admin .bracket-wrapper { display: flex; overflow-x: auto; padding: 2rem; gap: 3rem; }
        .turnamen-recap-admin .bracket-round { display: flex; flex-direction: column; justify-content: space-around; gap: 2rem; }
        .turnamen-recap-admin .bracket-match {
            display: flex; flex-direction: column;
            border: 1px solid rgba(255,255,255,0.06);
            background-color: rgba(15, 26, 15, 0.5);
            border-radius: 12px; width: 240px; overflow: hidden; position: relative;
        }
        .turnamen-recap-admin .bracket-player {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.7rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 0.88rem;
        }
        .turnamen-recap-admin .bracket-player:last-child { border-bottom: none; }
        .turnamen-recap-admin .bracket-player.winner { background: linear-gradient(90deg, rgba(57, 211, 83, 0.12) 0%, rgba(57, 211, 83, 0.03) 100%); font-weight: 700; color: var(--primary); }
        .turnamen-recap-admin .bracket-player.bye { color: var(--text-muted); font-style: italic; }
        .turnamen-recap-admin .bracket-score { font-weight: 800; color: var(--primary); font-size: 1rem; }
        
        /* Responsive adjustments */
        .recap-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .recap-title { font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; text-transform: uppercase; }
        .recap-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 3rem; }
        .recap-content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 3rem; }
        
        @media (max-width: 768px) {
            .recap-header { flex-direction: column; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; }
            .recap-title { font-size: 1.5rem; }
            .recap-stats-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 2rem; }
            .recap-content-grid { grid-template-columns: 1fr; gap: 1rem; margin-bottom: 2rem; }
            .turnamen-recap-admin .glass-card { padding: 1.25rem; }
            .turnamen-recap-admin { padding: 1rem !important; }
        }
    </style>
    
    <div class="turnamen-recap-admin" style="background-color: #0d1117; color: #c9d1d9; padding: 2rem; border-radius: 12px; font-family: 'Inter', sans-serif;">
        
        <!-- 1. Tournament Name -->
        <div class="recap-header">
            <h1 class="recap-title">
                <?php echo e($this->record->name); ?>

            </h1>
            <div style="display: flex; gap: 1rem;">
                <span style="background-color: rgba(43, 82, 255, 0.2); color: #58a6ff; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">
                    <?php echo e($this->record->status); ?>

                </span>
                <span style="background-color: rgba(57, 211, 83, 0.1); color: #39d353; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem;">
                    <?php echo e($this->record->entries()->count()); ?> / <?php echo e($this->record->max_entries); ?> Slot
                </span>
            </div>
        </div>

        <!-- 2. 4 Horizontal Cards -->
        <div class="recap-stats-grid">
            <div class="glass-card" style="padding: 1.5rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Pendaftaran Ditutup</div>
                <div style="color: var(--text-main); font-size: 1.1rem; font-weight: 700;"><?php echo e(\Carbon\Carbon::parse($this->record->registration_end)->format('d M Y H:i')); ?> WIB</div>
            </div>
            
            <div class="glass-card" style="padding: 1.5rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Turnamen Dimulai</div>
                <div style="color: var(--primary, #39d353); font-size: 1.1rem; font-weight: 700;"><?php echo e(\Carbon\Carbon::parse($this->record->tournament_start)->format('d M Y H:i')); ?> WIB</div>
            </div>

            <div class="glass-card" style="padding: 1.5rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Biaya per Slot</div>
                <div style="color: var(--text-main); font-size: 1.1rem; font-weight: 700;">Rp <?php echo e(number_format($this->record->price_per_slot, 0, ',', '.')); ?></div>
            </div>

            <div class="glass-card" style="padding: 1.5rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Peserta</div>
                <div style="color: var(--text-main); font-size: 1.1rem; font-weight: 700;"><?php echo e($this->leaderboard->count()); ?> Orang</div>
            </div>
        </div>

        <!-- 3. Podium & Statistik -->
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--primary, #39d353); margin-bottom: 1.5rem;">Recap Turnamen</h2>
        <div class="recap-content-grid">
            
            <!-- Podium -->
            <div class="glass-card" style="padding: 1.5rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--primary, #39d353);"></div>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem;">JUARA</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $leaderboard->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem; width: 40px; text-align: center;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index === 0): ?> 🥇
                                <?php elseif($index === 1): ?> 🥈
                                <?php elseif($index === 2): ?> 🥉
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Juara <?php echo e($index + 1); ?></div>
                                <div style="font-size: 1.2rem; font-weight: 700;"><?php echo e($row->player->name); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leaderboard->isEmpty()): ?>
                        <div style="color: var(--text-muted); font-style: italic;">Belum ada juara ditentukan.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <!-- Top Skor -->
            <div class="glass-card" style="padding: 1.5rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--secondary, #58a6ff);"></div>
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem;">TOP SKOR</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $topScorers->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <div style="font-weight: 600; font-size: 1rem;"><?php echo e($index + 1); ?>. <?php echo e($row->player->name); ?></div>
                            <div style="font-weight: 700;"><?php echo e($row->total_goals_scored); ?> Gol</div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topScorers->isEmpty()): ?>
                        <div style="color: var(--text-muted); font-style: italic;">Belum ada data top skor.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

        </div>

        <!-- 4. Bagan Turnamen -->
        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--primary, #39d353); margin-bottom: 1.5rem;">Bagan Turnamen</h2>
        
        <div class="glass-card" style="padding: 2rem; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeStageId): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($playersInStage)): ?>
                    <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
                        <label for="playerHighlight" style="font-size: 0.95rem; font-weight: 600;">Sorot Pemain:</label>
                        <select wire:model.live="highlightedPlayerId" id="playerHighlight" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: #161b22; color: #c9d1d9; font-size: 0.95rem; min-width: 200px;">
                            <option value="">-- Tampilkan Semua --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $playersInStage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p['id']); ?>"><?php echo e($p['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($rounds)): ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">
                        Bagan pertandingan belum digenerate.
                    </p>
                <?php else: ?>
                    <?php echo $__env->make('_partials.bracket-tree', ['bracketRounds' => $rounds, 'activeEntryIds' => $activeEntryIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">
                    Turnamen belum dimulai atau belum ada stage.
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\TurnamenPS\resources\views/filament/resources/tournaments/pages/recap-tournament.blade.php ENDPATH**/ ?>