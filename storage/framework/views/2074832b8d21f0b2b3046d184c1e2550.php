<div class="container" wire:poll.3s="checkIncomingCalls">
    
    <!-- Top Header & Audio Settings -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: clamp(1.5rem, 4vw, 2.25rem); font-weight: 800;">
                <?php echo e(__('Player Dashboard')); ?>

            </h2>
            <p style="color: var(--text-muted); font-size: clamp(0.8rem, 2.5vw, 0.95rem);"><?php echo e(__('Selamat datang kembali,')); ?> <strong><?php echo e($player->name); ?></strong></p>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <button onclick="togglePaymentDrawer(true)" class="btn btn-secondary" style="padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; border: 1px solid var(--border-color); cursor: pointer; white-space: nowrap;">
                <?php echo e(app()->getLocale() == 'id' ? 'Status Pembayaran' : 'Payment Status'); ?>

            </button>
        </div>
    </div>

    <!-- PERSONAL STATISTICS (Horizontal) -->
    <div style="margin-bottom: 3rem;">
        <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; margin-bottom: 1.5rem; color: var(--primary);">
            <?php echo e(__('Statistik Lintas Slot Anda')); ?>

        </h3>
        <?php
            $aggregates = \App\Models\TournamentPlayerAggregate::where('player_id', $player->id)->get();
            $totalGoals = $aggregates->sum('total_goals_scored');
            $totalMatches = $aggregates->sum('total_matches_played');
            $totalWins = $aggregates->sum('total_wins');
            $winRatio = $totalMatches > 0 ? round(($totalWins / $totalMatches) * 100) . '%' : '0%';

            $allMatchParticipants = \App\Models\MatchParticipant::whereHas('entry', function($q) use ($player) {
                    $q->where('player_id', $player->id);
                })
                ->join('matches', 'match_participants.match_id', '=', 'matches.id')
                ->whereIn('matches.status', ['completed', 'walkover'])
                ->orderBy('matches.finished_at', 'asc')
                ->select('match_participants.*', 'matches.finished_at')
                ->get();

            $currentStreak = 0;
            $bestStreak = 0;

            foreach ($allMatchParticipants as $mp) {
                if ($mp->is_winner === true || $mp->is_winner === 1) {
                    $currentStreak++;
                    if ($currentStreak > $bestStreak) {
                        $bestStreak = $currentStreak;
                    }
                } else {
                    $currentStreak = 0;
                }
            }
        ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; text-align: center;">
            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Total Goal</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--text-main); margin-top: 0.25rem;">
                    <?php echo e($totalGoals); ?>

                </div>
            </div>

            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Win Streak</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--text-main); margin-top: 0.25rem;">
                    <?php echo e($currentStreak); ?>

                </div>
            </div>

            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Rasio Menang</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--text-main); margin-top: 0.25rem;">
                    <?php echo e($winRatio); ?>

                </div>
            </div>

            <div class="card" style="padding: 1.25rem;">
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Rekor Win Streak</span>
                <div class="stat-card-value" style="font-size: clamp(1.75rem, 5vw, 2.25rem); font-weight: 800; color: var(--text-main); margin-top: 0.25rem;">
                    <?php echo e($bestStreak); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- TOURNAMENTS AVAILABLE FOR PURCHASE -->
    <div style="margin-bottom: 3rem;">
        <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; margin-bottom: 1.5rem; color: var(--primary);">
            <?php echo e(app()->getLocale() == 'id' ? 'Pendaftaran Turnamen Dibuka' : 'Open Tournaments'); ?>

        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $openTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <span class="badge badge-success" style="margin-bottom: 0.75rem;">REGISTRATION OPEN</span>
                        <h4 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo e($t->name); ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem;">
                            <?php echo e(__('Game')); ?>: <?php echo e($t->game_title); ?>

                        </p>
                        <?php
                            $verifiedCount = $t->entries()->count();
                            $pendingOverall = \App\Models\EntryBatch::where('tournament_id', $t->id)->where('status', 'pending')->sum('slot_count');
                            $availSlots = max(0, $t->max_entries - ($verifiedCount + $pendingOverall));
                        ?>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem;">
                            Slot Tersedia: <strong style="color: var(--primary);"><?php echo e($availSlots); ?></strong> dari <strong><?php echo e($t->max_entries); ?></strong>
                        </p>
                        <p style="color: var(--danger); font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem;">
                            <?php echo e(app()->getLocale() == 'id' ? 'Batas Pendaftaran' : 'Registration Deadline'); ?>: 
                            <?php echo e($t->registration_end->format('d M Y H:i')); ?>

                        </p>
                        <div style="margin-bottom: 1.5rem;">
                            <span style="font-size: 0.9rem; color: var(--text-muted);"><?php echo e(__('Harga per Slot')); ?>:</span>
                            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-top: 0.25rem;">
                                Rp <?php echo e(number_format($t->price_per_slot, 0, ',', '.')); ?>

                            </div>
                        </div>
                    </div>

                    <button wire:click="selectTournamentForPurchase(<?php echo e($t->id); ?>)" class="btn btn-primary" style="width: 100%;">
                        <?php echo e(app()->getLocale() == 'id' ? 'Beli Slot / Ikuti' : 'Purchase Slot'); ?>

                    </button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--text-muted);">
                    <?php echo e(app()->getLocale() == 'id' ? 'Tidak ada turnamen yang membuka pendaftaran saat ini.' : 'No tournaments are accepting registrations right now.'); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>


    <!-- ACTIVE SLOTS -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeEntries->isNotEmpty()): ?>
        <div style="margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                    <?php echo e(__('Your Active Slots')); ?>

                </h3>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $activeEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $t = $entry->tournament;
                    ?>
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; gap: 1rem;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                <span style="font-size: 1.15rem; font-weight: 800; color: var(--text-main);"><?php echo e($entry->display_name); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->status === 'verified'): ?>
                                        <span class="badge badge-success">AKTIF</span>
                                    <?php else: ?>
                                        <span class="badge <?php echo e(in_array($entry->status, ['active','champion']) ? 'badge-success' : 'badge-warning'); ?>">
                                            <?php echo e(strtoupper($entry->status)); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                    <?php echo e(__('Tournament')); ?>: <strong><?php echo e($t->name); ?></strong>
                                </p>
                                <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                                    Mulai Pertandingan: <span style="font-weight: 600;"><?php echo e($t->tournament_start->format('d M Y H:i')); ?> WIB</span>
                                </p>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->status === 'verified'): ?>
                                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem; background: var(--bg-surface); padding: 0.4rem; border-radius: 6px; border: 1px solid var(--border-color);">
                                        Slot Anda telah terverifikasi dan aktif di dalam turnamen.
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->status === 'champion'): ?>
                                    <p style="margin-top: 0.5rem; font-size: 1rem; font-weight: 700; color: gold;">Juara!</p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- TOURNAMENT TREE BRACKET AND HISTORY -->
    <div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 style="font-size: clamp(1.15rem, 3vw, 1.5rem); font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <?php echo e(__('Riwayat & Bagan Turnamen Anda')); ?>

            </h3>

        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($this->myTournaments) || count($this->myTournaments) === 0): ?>
            <div style="text-align: center; padding: 2.5rem; color: var(--text-muted); font-size: 0.9rem;">
                Anda belum memiliki riwayat turnamen atau pendaftaran yang terverifikasi.
            </div>
        <?php else: ?>
            
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->myTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button 
                        type="button"
                        wire:click="selectBracketTournament(<?php echo e($t->id); ?>)" 
                        class="btn"
                        style="
                            font-size: 0.85rem; 
                            padding: 0.5rem 1.25rem; 
                            border-radius: 30px; 
                            font-weight: 700;
                            border: 1px solid <?php echo e($this->selectedBracketTournamentId === $t->id ? 'var(--primary)' : 'var(--border-color)'); ?>;
                            background: <?php echo e($this->selectedBracketTournamentId === $t->id ? 'var(--primary)' : 'var(--bg-surface)'); ?>;
                            color: <?php echo e($this->selectedBracketTournamentId === $t->id ? '#000' : 'var(--text-main)'); ?>;
                            cursor: pointer;
                            transition: all 0.2s;
                        "
                    >
                        <?php echo e($t->name); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->selectedBracketTournamentId): ?>
                <?php
                    $selTournament = \App\Models\Tournament::find($this->selectedBracketTournamentId);
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selTournament): ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->bracketStages) > 1): ?>
                            <div style="display: flex; justify-content: flex-start; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                                
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                    <span style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Fase:</span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->bracketStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <button 
                                            type="button"
                                            wire:click="selectBracketStage(<?php echo e($stage->id); ?>)" 
                                            style="
                                                font-size: 0.75rem; 
                                                padding: 0.35rem 0.85rem; 
                                                border-radius: 6px; 
                                                font-weight: 600; 
                                                cursor: pointer; 
                                                border: 1px solid <?php echo e($this->selectedBracketStageId === $stage->id ? 'var(--primary)' : 'var(--border-color)'); ?>; 
                                                background: <?php echo e($this->selectedBracketStageId === $stage->id ? 'rgba(57, 211, 83, 0.1)' : 'transparent'); ?>; 
                                                color: <?php echo e($this->selectedBracketStageId === $stage->id ? 'var(--primary)' : 'var(--text-muted)'); ?>;
                                                transition: all 0.2s;
                                            "
                                        >
                                            <?php echo e($stage->name); ?>

                                        </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div style="margin-bottom: 2rem;">
                            <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--primary);">
                                <?php echo e(__('Tournament Bracket')); ?>

                            </h4>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($this->bracketRounds) || count($this->bracketRounds) === 0): ?>
                                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); font-size: 0.88rem; border: 1px dashed var(--border-color); border-radius: 12px; background: var(--bg-surface);">
                                    Bagan pertandingan untuk turnamen ini belum digenerate oleh admin.
                                </div>
                            <?php else: ?>
                                <?php echo $__env->make('_partials.bracket-tree', ['bracketRounds' => $this->bracketRounds, 'activeEntryIds' => $this->activeEntryIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selTournament->status !== 'completed'): ?>
                            
                            <div class="grid grid-cols-1" style="margin-top: 1rem; gap: 2rem; display: grid;">
                                <!-- Jadwal & Sedang Berjalan -->
                                <div>
                                    <h4 style="margin-bottom: 1.5rem; font-weight: 700; color: var(--secondary); font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                                        Jadwal Aktif
                                    </h4>
                                    <div style="display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                                        <?php
                                            $upcomingMatches = collect($this->bracketMyMatches)->filter(fn($m) => !in_array($m->status, ['completed', 'walkover']));
                                        ?>
                                    <div class="custom-scrollbar" style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 400px; overflow-y: auto;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $upcomingMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php
                                                $homePart = $match->participants->where('side', 'home')->first();
                                                $awayPart = $match->participants->where('side', 'away')->first();
                                                $homeName = $homePart?->entry?->display_name ?? 'TBD';
                                                $awayName = $awayPart?->entry?->display_name ?? 'TBD';
                                                $isHomeMe = in_array($homePart?->tournament_entry_id, $this->activeEntryIds);
                                                
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
                                                            <span style="font-size: 0.75rem; color: var(--primary); font-weight: 700;">
                                                                🎮 <?php echo e($match->psUnit->name); ?>

                                                            </span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                                <div style="display: flex; align-items: center; font-weight: 700; font-size: 0.95rem;">
                                                    <div style="flex: 1; min-width: 0;">
                                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: <?php echo e($isHomeMe ? 'var(--primary)' : 'var(--text-main)'); ?>; font-weight: <?php echo e($isHomeMe ? '800' : '700'); ?>;"><?php echo e($homeName); ?></div>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($homePart?->club?->name): ?>
                                                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($homePart->club->name); ?></div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <div style="flex-shrink: 0; text-align: center; padding: 0 0.5rem; color: var(--text-muted); font-size: 0.75rem;">
                                                        VS
                                                    </div>
                                                    <div style="flex: 1; min-width: 0; text-align: right;">
                                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: <?php echo e(!$isHomeMe ? 'var(--primary)' : 'var(--text-main)'); ?>; font-weight: <?php echo e(!$isHomeMe ? '800' : '700'); ?>;"><?php echo e($awayName); ?></div>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($awayPart?->club?->name): ?>
                                                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo e($awayPart->club->name); ?></div>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>


                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <div style="text-align: center; padding: 2.5rem 1.5rem; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; font-size: 0.95rem; background: var(--bg-surface);">
                                                Belum ada jadwal pertandingan.
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <!-- Selesai (Disembunyikan sesuai permintaan) -->
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- SLOT PURCHASE MODAL -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedTournamentId): ?>
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div class="card" style="max-width: 540px; width: 100%; max-height: 90vh; overflow-y: auto; border: 1px solid var(--border-color); background-color: var(--bg-card); padding: clamp(1rem, 3vw, 2rem); border-radius: 16px;">
                <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">
                    <?php echo e(__('Beli Slot Turnamen')); ?>

                </h4>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($purchaseStep === 1): ?>
                    <!-- STEP 1: SELECT QUANTITY -->
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Min. Slot per Peserta:</span>
                            <strong style="color: var(--text-main);"><?php echo e(\App\Models\Tournament::find($selectedTournamentId)?->min_slots_per_player ?: 1); ?> Slot</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Maks. Slot per Peserta:</span>
                            <strong style="color: var(--text-main);"><?php echo e(\App\Models\Tournament::find($selectedTournamentId)?->max_slot_per_player); ?> Slot</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Sisa Slot Tersedia:</span>
                            <strong style="color: var(--primary);"><?php echo e($remainingOverall); ?> Slot</strong>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="slot_count"><?php echo e(__('Jumlah Slot')); ?></label>
                        <input type="number" id="slot_count" wire:model.live.debounce.500ms="slot_count" class="form-control" min="<?php echo e(\App\Models\Tournament::find($selectedTournamentId)?->min_slots_per_player ?: 1); ?>" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['slot_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span style="color: var(--danger); font-size: 0.85rem;"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div style="background: rgba(59, 130, 246, 0.1); border-radius: 8px; padding: 0.75rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <span style="font-weight: 600; font-size: 0.95rem;"><?php echo e(__('Total Harga')); ?></span>
                        <span style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">Rp <?php echo e(number_format($total_price, 0, ',', '.')); ?></span>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="button" wire:click="$set('selectedTournamentId', null)" class="btn btn-secondary">Batal</button>
                        <button type="button" wire:click="proceedToPayment" class="btn btn-primary">Lanjut ke Pembayaran</button>
                    </div>
                <?php else: ?>
                    <!-- STEP 2: CHOOSE PAYMENT METHOD & SUBMIT -->
                    <form wire:submit.prevent="submitPurchase">
                        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.25rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Jumlah Slot Dipesan:</span>
                                <strong style="color: var(--text-main);"><?php echo e($slot_count); ?> Slot</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Total Pembayaran:</span>
                                <strong style="color: var(--primary);">Rp <?php echo e(number_format($total_price, 0, ',', '.')); ?></strong>
                            </div>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;"><?php echo e(__('Metode Pembayaran')); ?></label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <!-- QRIS Tab -->
                                <button type="button" 
                                        wire:click="$set('payment_method', 'qris')"
                                        style="flex: 1; padding: 1rem; border-radius: 12px; border: 2px solid <?php echo e($payment_method === 'qris' ? 'var(--primary)' : 'var(--border-color)'); ?>; background: <?php echo e($payment_method === 'qris' ? 'rgba(0, 230, 118, 0.1)' : 'var(--bg-surface)'); ?>; color: <?php echo e($payment_method === 'qris' ? 'var(--primary)' : 'var(--text-main)'); ?>; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                                    <span>QRIS</span>
                                </button>
                                <!-- Cash Tab -->
                                <button type="button" 
                                        wire:click="$set('payment_method', 'cash')"
                                        style="flex: 1; padding: 1rem; border-radius: 12px; border: 2px solid <?php echo e($payment_method === 'cash' ? 'var(--primary)' : 'var(--border-color)'); ?>; background: <?php echo e($payment_method === 'cash' ? 'rgba(0, 230, 118, 0.1)' : 'var(--bg-surface)'); ?>; color: <?php echo e($payment_method === 'cash' ? 'var(--primary)' : 'var(--text-main)'); ?>; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                                    <span>Cash / Tunai</span>
                                </button>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment_method === 'qris'): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment_info): ?>
                                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.9rem;">
                                    <strong style="color: var(--primary); display: block; margin-bottom: 0.25rem;">Informasi QRIS:</strong>
                                    <p style="white-space: pre-line; margin: 0;"><?php echo e($payment_info); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="form-group" style="margin-bottom: 1.5rem;">
                                <label class="form-label" for="payment_proof"><?php echo e(__('Bukti Pembayaran QRIS')); ?> (Format Gambar, Maks 2MB)</label>
                                <input type="file" id="payment_proof" wire:model="payment_proof" class="form-control" accept="image/*" required>
                                <div wire:loading wire:target="payment_proof" style="color: var(--primary); font-size: 0.85rem; margin-top: 0.25rem;">
                                    Mengunggah gambar...
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['payment_proof'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span style="color: var(--danger); font-size: 0.85rem;"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div style="background: rgba(0, 230, 118, 0.05); border: 1px solid var(--primary); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem; line-height: 1.5; color: var(--text-main);">
                                <strong>Instruksi Pembayaran Cash:</strong>
                                <p style="margin: 0.5rem 0 0 0; color: var(--text-muted);">Silakan lakukan pembayaran tunai ke Admin di lokasi. Pendaftaran dan pengisian slot Anda akan segera diverifikasi oleh Admin setelah pembayaran tunai diterima secara langsung.</p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                            <button type="button" wire:click="$set('purchaseStep', 1)" class="btn btn-secondary">Kembali</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <?php echo e($payment_method === 'qris' ? 'Kirim Bukti QRIS' : 'Ajukan Pendaftaran (Cash)'); ?>

                            </button>
                        </div>
                    </form>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- DISPUTE MODAL -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedMatchId): ?>
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div class="card" style="max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto; border: 1px solid var(--border-color); background-color: var(--bg-card); padding: clamp(1rem, 3vw, 2rem); border-radius: 16px;">
                <h4 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">
                    <?php echo e(__('Raise Dispute')); ?>

                </h4>

                <form wire:submit.prevent="submitDispute">
                    <div class="form-group">
                        <label class="form-label"><?php echo e(__('Dispute Reason')); ?></label>
                        <textarea class="form-control" wire:model.defer="disputeReason" rows="5" placeholder="Sebutkan detail ketidaksesuaian skor..." required></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['disputeReason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span style="color: var(--danger); font-size: 0.85rem;"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="button" wire:click="$set('selectedMatchId', null)" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background: var(--danger); box-shadow: none;">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- TOAST ALERT -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($toastMessage): ?>
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
            style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); padding: 1rem 1.5rem; border-radius: 50px; font-weight: 600; box-shadow: 0 10px 15px rgba(0,0,0,0.3); z-index: 9999; max-width: calc(100vw - 2rem); cursor: pointer; text-align: center; white-space: nowrap;"
            :style="$wire.toastType === 'error' ? 'background: var(--danger); color: white;' : 'background: var(--primary); color: #000;'"
            wire:click="dismissToast"
        >
            <?php echo e($toastType === 'error' ? '' : ''); ?> <?php echo e($toastMessage); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- PAYMENT HISTORY DRAWER (Right Pop-up) -->
    <div id="paymentDrawerBackdrop" class="payment-drawer-backdrop" onclick="togglePaymentDrawer(false)"></div>
    <div id="paymentDrawer" class="payment-drawer" wire:ignore>
        <div class="payment-drawer-header">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--primary);">
                <?php echo e(app()->getLocale() == 'id' ? 'Status Pembayaran Slot' : 'Payment Status History'); ?>

            </h3>
            <button class="payment-drawer-close" onclick="togglePaymentDrawer(false)">&times;</button>
        </div>
        
        <div class="payment-drawer-content">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="payment-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; gap: 0.5rem;">
                        <span style="font-weight: 700; font-size: 1rem; color: var(--text-main); line-height: 1.2;">
                            <?php echo e($batch->tournament->name); ?>

                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($batch->status === 'verified'): ?>
                            <span class="badge badge-success" style="font-size: 0.75rem;">VERIFIED</span>
                        <?php elseif($batch->status === 'pending'): ?>
                            <span class="badge badge-warning" style="font-size: 0.75rem;">PENDING</span>
                        <?php else: ?>
                            <span class="badge badge-danger" style="font-size: 0.75rem;">REJECTED</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    
                    <div style="font-size: 0.88rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span><?php echo e(app()->getLocale() == 'id' ? 'Jumlah Slot' : 'Slot Count'); ?>:</span>
                            <strong style="color: var(--text-main);"><?php echo e($batch->slot_count); ?> Slot</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span><?php echo e(__('Total Harga')); ?>:</span>
                            <strong style="color: var(--primary);">Rp <?php echo e(number_format($batch->total_price, 0, ',', '.')); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span><?php echo e(app()->getLocale() == 'id' ? 'Metode' : 'Method'); ?>:</span>
                            <strong style="color: var(--text-main); text-transform: uppercase;"><?php echo e($batch->payment_method); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span><?php echo e(app()->getLocale() == 'id' ? 'Tanggal' : 'Date'); ?>:</span>
                            <strong><?php echo e($batch->created_at->format('d M Y H:i')); ?> WIB</strong>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($batch->status === 'rejected' && $batch->rejection_reason): ?>
                            <div style="margin-top: 0.5rem; padding: 0.5rem; background: rgba(211, 47, 47, 0.05); border-left: 3px solid var(--danger); border-radius: 4px; color: var(--danger); font-size: 0.82rem;">
                                <strong><?php echo e(app()->getLocale() == 'id' ? 'Alasan Penolakan:' : 'Rejection Reason:'); ?></strong>
                                <div style="margin-top: 0.15rem; word-break: break-word;"><?php echo e($batch->rejection_reason); ?></div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="text-align: center; color: var(--text-muted); margin-top: 4rem;">
                    <?php echo e(app()->getLocale() == 'id' ? 'Belum ada riwayat transaksi.' : 'No transaction history found.'); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

</div>
<?php /**PATH C:\laragon\www\TurnamenPS\resources\views/livewire/player/dashboard.blade.php ENDPATH**/ ?>