<!-- Tournament Header -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
    <div>
        <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">
            <?php echo e($tournament->name); ?>

        </h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;"><?php echo e($tournament->game_title); ?></p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <?php
            $statusColors = [
                'draft' => ['bg' => 'rgba(107,114,128,0.15)', 'text' => '#6B7280'],
                'registration' => ['bg' => 'rgba(57,211,83,0.15)', 'text' => 'var(--primary)'],
                'ongoing' => ['bg' => 'rgba(255,193,7,0.15)', 'text' => '#FFC107'],
                'completed' => ['bg' => 'rgba(59,130,246,0.15)', 'text' => '#3B82F6'],
            ];
            $sc = $statusColors[$tournament->status] ?? $statusColors['draft'];
        ?>
        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.35rem 0.75rem; border-radius: 6px; background: <?php echo e($sc['bg']); ?>; color: <?php echo e($sc['text']); ?>;">
            <?php echo e(strtoupper($tournament->status)); ?>

        </span>
        <span style="font-size: 0.82rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.75rem; border-radius: 6px; background: rgba(57,211,83,0.15);">
            <?php echo e($tournament->entries()->count()); ?> / <?php echo e($tournament->max_entries); ?> Slot
        </span>
    </div>
</div>

<!-- Tournament Info -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Pendaftaran Ditutup</div>
        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><?php echo e($tournament->registration_end->format('d M Y H:i')); ?> WIB</div>
    </div>
    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Turnamen Dimulai</div>
        <div style="font-size: 0.95rem; font-weight: 700; color: var(--primary);"><?php echo e($tournament->tournament_start->format('d M Y H:i')); ?> WIB</div>
    </div>
    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Biaya Per Slot</div>
        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">Rp <?php echo e(number_format($tournament->price_per_slot, 0, ',', '.')); ?></div>
    </div>
    <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
        <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.35rem;">Total Peserta</div>
        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><?php echo e($tournament->entries()->distinct('player_id')->count('player_id')); ?> Orang</div>
    </div>
</div>
<?php /**PATH C:\laragon\www\TurnamenPS\resources\views/_partials/tournament-header.blade.php ENDPATH**/ ?>