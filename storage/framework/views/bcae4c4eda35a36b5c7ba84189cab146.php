<div style="width: 100%; overflow-x: hidden;" wire:poll.3s="refreshData">
    <!-- HERO SECTION -->
    <div class="hero-section" style="position: relative; min-height: calc(100vh - 60px); width: 100vw; margin-left: calc(-50vw + 50%); background: linear-gradient(to bottom, rgba(10, 15, 10, 0.65), rgba(10, 15, 10, 0.95)), url('/images/hero-bg.png') no-repeat center center / cover; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; overflow: hidden; padding-left: 2rem; padding-right: 2rem; padding-bottom: 2rem;">
        <!-- Subtle Glow Effects -->
        <div style="position: absolute; top: 0%; left: 0%; width: 50vw; height: 50vw; background: rgba(57,211,83,0.1); border-radius: 50%; filter: blur(100px); z-index: 0; pointer-events: none;"></div>
        <div style="position: absolute; bottom: 0%; right: 0%; width: 60vw; height: 60vw; background: rgba(37,122,53,0.08); border-radius: 50%; filter: blur(120px); z-index: 0; pointer-events: none;"></div>
        
        <div style="position: relative; z-index: 1; max-width: 800px; padding: 0 1rem; width: 100%;">
            <div class="hero-pill" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem; border-radius: 100px; font-size: 0.85rem; font-weight: 600; letter-spacing: 1px; margin-bottom: 2rem; text-transform: uppercase; animation: fadeInDown 0.8s ease-out forwards; opacity: 0;">
                <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; display: inline-block;"></span>
                Welcome to the Arena
            </div>
            
            <h1 style="font-size: clamp(3rem, 7vw, 5.5rem); font-weight: 900; margin-bottom: 1.5rem; line-height: 1.1; letter-spacing: -2px; color: var(--text-main); display: flex; flex-direction: column; align-items: center; justify-content: center; text-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                <span class="typewriter-text" style="display: inline-block; overflow: hidden; white-space: nowrap; border-right: .15em solid var(--primary); margin: 0 auto; letter-spacing: -2px;">
                    Infinity <span class="gradient-text">Boxzone</span>
                </span>
            </h1>
            
            <p x-data="{ 
                text: '', 
                fullText: 'Pusat kompetisi eFootball & PES terbaik. Daftar turnamen, pantau bagan pertandingan secara real-time, dan jadilah yang terbaik.',
                typeWriter() {
                    let i = 0;
                    let speed = 30; // ms per char
                    let interval = setInterval(() => {
                        this.text += this.fullText.charAt(i);
                        i++;
                        if (i >= this.fullText.length) {
                            clearInterval(interval);
                        }
                    }, speed);
                }
               }" 
               x-init="setTimeout(() => typeWriter(), 1800)"
               style="font-size: clamp(1.1rem, 2vw, 1.35rem); color: var(--text-main); text-shadow: 0 2px 10px rgba(0,0,0,0.6); margin-bottom: 3.5rem; line-height: 1.6; max-width: 650px; margin-left: auto; margin-right: auto; font-weight: 400; min-height: 4.5rem; animation: fadeInUp 1s ease-out 1s forwards; opacity: 0;">
                <span x-text="text"></span><span x-show="text.length < fullText.length" style="border-right: .15em solid var(--primary); animation: blink-caret .75s step-end infinite;">&nbsp;</span>
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; animation: fadeInUp 1s ease-out 1.2s forwards; opacity: 0;">
                <a href="#open-tournaments" class="btn btn-primary" style="padding: 1rem 3rem; border-radius: 100px; font-weight: 700; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(57, 211, 83, 0.25); text-transform: uppercase; letter-spacing: 1px;">
                    Daftar Turnamen
                </a>
                <a href="#ongoing-tournaments" class="btn btn-secondary" style="padding: 1rem 3rem; border-radius: 100px; font-weight: 700; font-size: 1.1rem; background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.2); color: var(--text-main); text-transform: uppercase; letter-spacing: 1px; text-shadow: 0 1px 4px rgba(0,0,0,0.4);">
                    Jelajahi Bagan Live
                </a>
            </div>
        </div>
        
        <!-- Scroll indicator -->
        <div style="position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); animation: bounce 2s infinite; opacity: 0; animation-delay: 2s; animation-fill-mode: forwards;">
            <svg style="width: 24px; height: 24px; color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </div>
    </div>

    <!-- MAIN CONTENT CONTAINER -->
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">

        <!-- OPEN TOURNAMENTS SECTION -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($openTournaments->count() > 0): ?>
        <div id="open-tournaments" style="margin-bottom: 5rem; padding-top: 4rem;">
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 3.5rem; text-align: center;">
                <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text-main); letter-spacing: -0.5px;">
                Pendaftaran <span class="gradient-text">Dibuka</span>
            </h2>
            <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 500px;">Amankan slot Anda sebelum penuh dan bersiaplah untuk bertanding.</p>
            </div>
        
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $openTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tournament): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $pendingOverall = \App\Models\EntryBatch::where('tournament_id', $tournament->id)->where('status', 'pending')->sum('slot_count');
                        $verifiedCount = $tournament->entries_count;
                        $filledPercent = $tournament->max_entries > 0 ? min(100, round((($verifiedCount + $pendingOverall) / $tournament->max_entries) * 100)) : 0;
                        $availSlots = max(0, $tournament->max_entries - ($verifiedCount + $pendingOverall));
                    ?>
                    <div class="glass-card" style="padding: 1.75rem; border-radius: 16px; border: 1px solid var(--border-color); background: var(--bg-card); box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 25px var(--gradient-glow)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.05)';">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
                            <div style="display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.35rem 0.75rem; border-radius: 6px; background: var(--primary-glow); color: var(--primary-dark);">
                                Buka
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.15rem; color: var(--text-main); font-weight: 800;">
                                    Rp <?php echo e(number_format($tournament->price_per_slot, 0, ',', '.')); ?>

                                </div>
                                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 500; text-transform: uppercase;">per slot</div>
                            </div>
                        </div>
                        
                        <h3 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.25rem; color: var(--text-main); line-height: 1.3; text-transform: capitalize;">
                            <?php echo e($tournament->name); ?>

                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0.75rem; flex-grow: 1;">
                            <?php echo e($tournament->game_title); ?>

                        </p>

                        <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1.25rem; font-size: 0.82rem;">
                            <div style="display: flex; align-items: center; gap: 0.4rem; color: var(--text-muted);">
                                📅 Pendaftaran ditutup: <span style="color: var(--text-main); font-weight: 600;"><?php echo e($tournament->registration_end->format('d M Y H:i')); ?> WIB</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.4rem; color: var(--text-muted);">
                                🏟️ Turnamen dimulai: <span style="color: var(--primary); font-weight: 600;"><?php echo e($tournament->tournament_start->format('d M Y H:i')); ?> WIB</span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem; background: var(--bg-input); border-radius: 12px; padding: 1rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-muted);">Sisa Slot: <span style="color: var(--text-main);"><?php echo e($availSlots); ?></span></span>
                                <span style="color: var(--text-muted);">Total: <span style="color: var(--text-main);"><?php echo e($tournament->max_entries); ?></span></span>
                            </div>
                            <div style="height: 8px; background: rgba(0, 0, 0, 0.1); border-radius: 10px; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                                <div style="height: 100%; background: var(--primary); width: <?php echo e($filledPercent); ?>%; border-radius: 10px;"></div>
                            </div>
                        </div>
                        
                        <a href="<?php echo e(route('player.login')); ?>" class="btn btn-primary" style="width: 100%; text-align: center; padding: 0.85rem; border-radius: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            Detail & Daftar
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($ongoingTournaments) > 0): ?>
    <div id="ongoing-tournaments" style="margin-bottom: 5rem; padding-top: 2rem;">
        <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 3.5rem; text-align: center;">
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text-main); letter-spacing: -0.5px;">
                Turnamen <span class="gradient-text">Berjalan</span>
            </h2>
            <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 500px;">Bagan pertandingan dari turnamen yang sedang berlangsung saat ini.</p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ongoingTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tournament): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="glass-card" style="margin-bottom: 3rem; padding: 2rem; border-radius: 16px; overflow-x: auto;">
            <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; text-align: center; color: var(--text-main);">
                <?php echo e($tournament->name); ?>

            </h3>
            
            <div class="bracket-wrapper" style="min-width: 800px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tournament->baganLiveMatches->isNotEmpty()): ?>
                    <?php echo $__env->make('_partials.bracket-tree', ['bracketRounds' => $tournament->baganLiveMatches->toArray()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- Jadwal dan Hasil Pertandingan -->
            <div class="grid grid-cols-1 md-grid-cols-2" style="margin-top: 3rem; gap: 2rem; display: grid;">
                <!-- Jadwal & Sedang Berjalan -->
                <div>
                    <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--secondary); font-size: 1.1rem;">
                        ⚔️ Jadwal & Sedang Berjalan
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tournament->upcomingMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $homePart = $match->participants->where('side', 'home')->first();
                                $awayPart = $match->participants->where('side', 'away')->first();
                                $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                            ?>
                            <div class="soft-well" style="padding: 1rem 1.25rem; border-left: 3px solid <?php echo e($match->status === 'ongoing' ? 'var(--primary)' : 'var(--border-color)'); ?>; border-radius: 12px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="flex: 1;">
                                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.2rem 0.6rem; border-radius: 6px; background: <?php echo e($match->status === 'ongoing' ? 'rgba(57,211,83,0.15)' : 'rgba(255,193,7,0.15)'); ?>; color: <?php echo e($match->status === 'ongoing' ? 'var(--primary)' : '#FFC107'); ?>;">
                                            <?php echo e(strtoupper($match->status)); ?>

                                        </span>
                                    </div>
                                    <div style="flex: 1; text-align: center;">
                                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.2rem 0.6rem; border-radius: 6px; background: var(--bg-surface); color: var(--text-muted);">
                                            <?php echo e($match->computedRoundName); ?>

                                        </span>
                                    </div>
                                    <div style="flex: 1; text-align: right;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->psUnit): ?>
                                            <span style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">
                                                🎮 <?php echo e($match->psUnit->name); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; font-weight: 700; font-size: 0.95rem;">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($homeName); ?></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($homePart?->club?->name): ?>
                                            <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($homePart->club->name); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div style="flex-shrink: 0; text-align: center; padding: 0 0.75rem; min-width: 70px;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->status === 'ongoing' || $match->status === 'completed' || $match->status === 'walkover'): ?>
                                            <div style="font-size: 1.1rem; letter-spacing: 2px; font-weight: 800; color: var(--primary);"><?php echo e($homePart?->goals_scored ?? 0); ?> - <?php echo e($awayPart?->goals_scored ?? 0); ?></div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->decided_by_penalty): ?>
                                                <div style="font-size: 0.65rem; letter-spacing: 1px; color: var(--text-muted); margin-top: 0.15rem;">(<?php echo e($match->penalty_score_home); ?>) - (<?php echo e($match->penalty_score_away); ?>)</div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 0.8rem; font-weight: 500;">VS</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div style="flex: 1; min-width: 0; text-align: right;">
                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($awayName); ?></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($awayPart?->club?->name): ?>
                                            <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($awayPart->club->name); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div style="text-align: center; padding: 2rem 0; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; font-size: 0.9rem;">
                                Tidak ada antrean match aktif.
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <!-- Selesai -->
                <div>
                    <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--text-muted); font-size: 1.1rem;">
                        ✅ Pertandingan Selesai
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tournament->completedMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $homePart = $match->participants->where('side', 'home')->first();
                                $awayPart = $match->participants->where('side', 'away')->first();
                                $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                                $homeScore = $match->status === 'walkover' && $homePart && $homePart->is_winner ? '3' : ($homePart ? $homePart->goals_scored : '-');
                                $awayScore = $match->status === 'walkover' && $awayPart && $awayPart->is_winner ? '3' : ($awayPart ? $awayPart->goals_scored : '-');
                                $roundName = $match->computedRoundName ?? 'Babak';
                            ?>
                            <div class="soft-well" style="padding: 1rem 1.25rem; border-left: 3px solid var(--border-color); border-radius: 12px; opacity: 0.8;">
                                <div style="text-align: center; margin-bottom: 0.5rem;">
                                    <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.2rem 0.6rem; border-radius: 6px; background: var(--bg-surface); color: var(--text-muted);">
                                        <?php echo e($roundName); ?>

                                    </span>
                                </div>
                                <div style="display: flex; align-items: center; font-weight: 700; font-size: 0.95rem;">
                                    <div style="flex: 1; min-width: 0; <?php echo e($homePart && $homePart->is_winner ? 'color: var(--primary);' : ''); ?>">
                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($homeName); ?></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($homePart?->club?->name): ?>
                                            <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($homePart->club->name); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div style="flex-shrink: 0; text-align: center; padding: 0 0.75rem; min-width: 70px;">
                                        <div style="font-size: 1.1rem; letter-spacing: 2px;"><?php echo e($homeScore); ?> - <?php echo e($awayScore); ?></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->decided_by_penalty): ?>
                                            <div style="font-size: 0.65rem; letter-spacing: 1px; color: var(--text-muted);">(<?php echo e($match->penalty_score_home); ?>) - (<?php echo e($match->penalty_score_away); ?>)</div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div style="flex: 1; min-width: 0; text-align: right; <?php echo e($awayPart && $awayPart->is_winner ? 'color: var(--primary);' : ''); ?>">
                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($awayName); ?></div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($awayPart?->club?->name): ?>
                                            <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($awayPart->club->name); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div style="text-align: center; padding: 2rem 0; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; font-size: 0.9rem;">
                                Belum ada pertandingan yang selesai.
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <!-- LATEST TOURNAMENT -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestTournament): ?>
        <div style="margin-bottom: 5rem; padding-top: 2rem;">
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 3.5rem; text-align: center;">
                <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text-main); letter-spacing: -0.5px;">
                    Turnamen <span class="gradient-text">Terbaru</span>
                </h2>
                <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 500px;">Turnamen terakhir yang diadakan beserta bagan dan jadwal pertandingannya.</p>
            </div>

            <div class="glass-card" style="padding: 2rem; border-radius: 16px; overflow-x: auto;">
                <!-- Tournament Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">
                            <?php echo e($latestTournament->name); ?>

                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem;"><?php echo e($latestTournament->game_title); ?></p>
                    </div>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <?php
                            $statusColors = [
                                'draft' => ['bg' => 'rgba(107,114,128,0.15)', 'text' => '#6B7280'],
                                'registration' => ['bg' => 'rgba(57,211,83,0.15)', 'text' => 'var(--primary)'],
                                'ongoing' => ['bg' => 'rgba(255,193,7,0.15)', 'text' => '#FFC107'],
                                'completed' => ['bg' => 'rgba(59,130,246,0.15)', 'text' => '#3B82F6'],
                            ];
                            $sc = $statusColors[$latestTournament->status] ?? $statusColors['draft'];
                        ?>
                        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.35rem 0.75rem; border-radius: 6px; background: <?php echo e($sc['bg']); ?>; color: <?php echo e($sc['text']); ?>;">
                            <?php echo e(strtoupper($latestTournament->status)); ?>

                        </span>
                        <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted); display: flex; align-items: center; gap: 0.3rem;">
                            👥 <?php echo e($latestTournament->entries_count); ?> Peserta
                        </span>
                    </div>
                </div>

                <!-- Tournament Info -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Pendaftaran Ditutup</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><?php echo e($latestTournament->registration_end->format('d M Y H:i')); ?> WIB</div>
                    </div>
                    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Turnamen Dimulai</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: var(--primary);"><?php echo e($latestTournament->tournament_start->format('d M Y H:i')); ?> WIB</div>
                    </div>
                    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Biaya Per Slot</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">Rp <?php echo e(number_format($latestTournament->price_per_slot, 0, ',', '.')); ?></div>
                    </div>
                    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Total Slot</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><?php echo e($latestTournament->max_entries); ?></div>
                    </div>
                </div>

                <!-- Bracket -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestTournament->baganLiveMatches->isNotEmpty()): ?>
                    <div style="margin-bottom: 2rem;">
                        <h4 style="font-weight: 700; color: var(--accent); font-size: 1.1rem; margin-bottom: 1rem;">
                            🌳 Bagan Pertandingan
                        </h4>
                        <div class="bracket-wrapper" style="min-width: 800px;">
                            <?php echo $__env->make('_partials.bracket-tree', ['bracketRounds' => $latestTournament->baganLiveMatches->toArray()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <!-- Match Schedule -->
                <div class="grid grid-cols-1 md-grid-cols-2" style="gap: 2rem;">
                    <!-- Upcoming & Ongoing -->
                    <div>
                        <h4 style="margin-bottom: 1rem; font-weight: 700; color: var(--secondary); font-size: 1.05rem;">
                            ⚔️ Jadwal & Sedang Berjalan
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $latestTournament->upcomingMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $homePart = $match->participants->where('side', 'home')->first();
                                    $awayPart = $match->participants->where('side', 'away')->first();
                                    $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                    $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                                ?>
                                <div class="soft-well" style="padding: 0.85rem 1rem; border-left: 3px solid <?php echo e($match->status === 'ongoing' ? 'var(--primary)' : 'var(--border-color)'); ?>; border-radius: 10px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                        <div style="flex: 1;">
                                            <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.15rem 0.5rem; border-radius: 5px; background: <?php echo e($match->status === 'ongoing' ? 'rgba(57,211,83,0.15)' : 'rgba(255,193,7,0.15)'); ?>; color: <?php echo e($match->status === 'ongoing' ? 'var(--primary)' : '#FFC107'); ?>;">
                                                <?php echo e(strtoupper($match->status)); ?>

                                            </span>
                                        </div>
                                        <div style="flex: 1; text-align: center;">
                                            <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.15rem 0.5rem; border-radius: 5px; background: var(--bg-surface); color: var(--text-muted);">
                                                <?php echo e($match->computedRoundName); ?>

                                            </span>
                                        </div>
                                        <div style="flex: 1; text-align: right;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->psUnit): ?>
                                                <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600;">🎮 <?php echo e($match->psUnit->name); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; font-weight: 700; font-size: 0.88rem;">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($homeName); ?></div>
                                        </div>
                                        <div style="flex-shrink: 0; text-align: center; padding: 0 0.6rem; min-width: 60px;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->status === 'ongoing' || $match->status === 'completed' || $match->status === 'walkover'): ?>
                                                <div style="font-size: 1rem; letter-spacing: 2px; font-weight: 800; color: var(--primary);"><?php echo e($homePart?->goals_scored ?? 0); ?> - <?php echo e($awayPart?->goals_scored ?? 0); ?></div>
                                            <?php else: ?>
                                                <span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 500;">VS</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div style="flex: 1; min-width: 0; text-align: right;">
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($awayName); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div style="text-align: center; padding: 1.5rem 0; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 10px; font-size: 0.85rem;">
                                    Tidak ada jadwal aktif.
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div>
                        <h4 style="margin-bottom: 1rem; font-weight: 700; color: var(--text-muted); font-size: 1.05rem;">
                            ✅ Pertandingan Selesai
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $latestTournament->completedMatches->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $homePart = $match->participants->where('side', 'home')->first();
                                    $awayPart = $match->participants->where('side', 'away')->first();
                                    $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                    $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                                    $homeScore = $match->status === 'walkover' && $homePart && $homePart->is_winner ? '3' : ($homePart ? $homePart->goals_scored : '-');
                                    $awayScore = $match->status === 'walkover' && $awayPart && $awayPart->is_winner ? '3' : ($awayPart ? $awayPart->goals_scored : '-');
                                    $roundName = $match->computedRoundName ?? 'Babak';
                                ?>
                                <div class="soft-well" style="padding: 0.85rem 1rem; border-left: 3px solid var(--border-color); border-radius: 10px; opacity: 0.8;">
                                    <div style="text-align: center; margin-bottom: 0.35rem;">
                                        <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.15rem 0.5rem; border-radius: 5px; background: var(--bg-surface); color: var(--text-muted);">
                                            <?php echo e($roundName); ?>

                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; font-weight: 700; font-size: 0.88rem;">
                                        <div style="flex: 1; min-width: 0; <?php echo e($homePart && $homePart->is_winner ? 'color: var(--primary);' : ''); ?>">
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($homeName); ?></div>
                                        </div>
                                        <div style="flex-shrink: 0; text-align: center; padding: 0 0.6rem; min-width: 60px;">
                                            <div style="font-size: 1rem; letter-spacing: 2px;"><?php echo e($homeScore); ?> - <?php echo e($awayScore); ?></div>
                                        </div>
                                        <div style="flex: 1; min-width: 0; text-align: right; <?php echo e($awayPart && $awayPart->is_winner ? 'color: var(--primary);' : ''); ?>">
                                            <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($awayName); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div style="text-align: center; padding: 1.5rem 0; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 10px; font-size: 0.85rem;">
                                    Belum ada pertandingan selesai.
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


    <!-- STATISTICS & RANKINGS -->
    <div id="rankings" style="margin-bottom: 5rem; padding-top: 2rem;">
        <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 3.5rem; text-align: center;">
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text-main); letter-spacing: -0.5px;">
                Global <span class="gradient-text">Statistik</span>
            </h2>
            <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 500px;">Pantau pemain terbaik lintas turnamen.</p>
        </div>
        
        <div class="stats-grid" style="display: grid; gap: 2rem;">
            
            <!-- TOP PLAYERS (Vertical) -->
            <div class="glass-card" style="border-radius: 16px; border: 1px solid var(--border-color); padding: 0; overflow: hidden; background: var(--bg-card); box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); background: rgba(0,0,0,0.02);">
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        🏆 Top Global Players
                    </h3>
                </div>
                <div style="display: flex; flex-direction: column;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topPlayers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $player): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='var(--gradient-glow)';" onmouseout="this.style.background='transparent';">
                            <div style="display: flex; align-items: center; gap: 1.25rem;">
                                <div style="font-size: 1.1rem; font-weight: 800; color: <?php echo e($index === 0 ? '#FBBF24' : ($index === 1 ? '#9CA3AF' : ($index === 2 ? '#D97706' : 'var(--text-muted)'))); ?>; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: <?php echo e($index === 0 ? 'rgba(251,191,36,0.1)' : ($index === 1 ? 'rgba(156,163,175,0.1)' : ($index === 2 ? 'rgba(217,119,6,0.1)' : 'transparent'))); ?>; border-radius: 8px;">
                                    <?php echo e($index + 1); ?>

                                </div>
                                <div>
                                    <div style="font-weight: 800; font-size: 1.05rem; color: var(--text-main); margin-bottom: 0.15rem;"><?php echo e($player->name); ?></div>
                                    <div style="font-size: 0.75rem; font-weight: 500; color: var(--text-muted);"><?php echo e($player->total_goals ?? 0); ?> Gol Dicetak</div>
                                </div>
                            </div>
                            <div style="text-align: right; background: var(--bg-input); padding: 0.4rem 0.8rem; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                                <div style="font-weight: 800; font-size: 1.1rem; color: var(--primary);"><?php echo e($player->total_wins ?? 0); ?></div>
                                <div style="font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">Wins</div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem; font-size: 0.95rem;">
                            Belum ada statistik pemain tercatat.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Add some minimal styles directly to the file if needed to avoid breaking external CSS, but using inline primarily for safety -->
    <style>
        /* Hero Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: var(--primary); }
        }
        @keyframes hide-caret {
            to { border-color: transparent; border-right-width: 0; }
        }
        .typewriter-text {
            animation: 
                typing 1.5s steps(30, end) 0.2s forwards,
                blink-caret .75s step-end 3,
                hide-caret 0.1s forwards 2.5s;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
            40% { transform: translateY(-15px) translateX(-50%); }
            60% { transform: translateY(-7px) translateX(-50%); }
        }
        
        @media (min-width: 768px) {
            .md-grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        }
        @media (min-width: 1024px) {
            .lg-grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
        }
        .glass-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    </style>
</div>
<?php /**PATH C:\laragon\www\TurnamenPS\resources\views/livewire/home.blade.php ENDPATH**/ ?>