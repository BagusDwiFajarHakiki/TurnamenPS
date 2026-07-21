<?php
    $thirdPlaceMatch = null;
    $filteredRounds = [];
    foreach ($bracketRounds as $rNum => $rMatches) {
        $filtered = array_values(array_filter($rMatches, function ($m) use (&$thirdPlaceMatch) {
            if ($m['bracket_position'] === '3rd_place') {
                $thirdPlaceMatch = $m;
                return false;
            }
            return true;
        }));
        if (!empty($filtered)) {
            $filteredRounds[$rNum] = $filtered;
        }
    }

    $roundNumbers = array_keys($filteredRounds);
    if (empty($roundNumbers)) {
        return;
    }
    $maxRound = max($roundNumbers);

    $leftRounds = [];
    $rightRounds = [];
    $finalRoundMatches = [];

    foreach ($filteredRounds as $roundNum => $matches) {
        if ($roundNum == $maxRound) {
            $finalRoundMatches = $matches;
        } else {
            $half = intdiv(count($matches), 2);
            $leftRounds[$roundNum] = array_values(array_slice($matches, 0, $half));
            $rightRounds[$roundNum] = array_values(array_slice($matches, $half));
        }
    }

    $getRoundName = function($rn) use ($maxRound) {
        $sl = $maxRound - $rn;
        if ($sl === 0) return 'FINAL';
        if ($sl === 1) return 'SEMIFINAL';
        if ($sl === 2) return 'PEREMPAT FINAL';
        return 'BABAK ' . pow(2, $sl + 1);
    };

    $buildGridData = function($rounds, $totalRows) {
        if (empty($rounds) || $totalRows === 0) return null;
        $roundNums = array_keys($rounds);
        $gridData = [];
        foreach ($roundNums as $roundNum) {
            $matches = $rounds[$roundNum];
            $n = count($matches);
            $span = $totalRows / $n;
            $items = [];
            foreach ($matches as $i => $match) {
                $items[] = [
                    'match' => $match,
                    'rowStart' => $i * $span + 1,
                    'rowEnd' => ($i + 1) * $span + 1,
                ];
            }
            $gridData[$roundNum] = $items;
        }
        return [
            'data' => $gridData,
            'totalRows' => $totalRows,
            'roundNums' => $roundNums,
            'roundCount' => count($roundNums),
        ];
    };

    $leftMax = !empty($leftRounds) ? max(array_map(fn($r) => count($r), $leftRounds)) : 0;
    $rightMax = !empty($rightRounds) ? max(array_map(fn($r) => count($r), $rightRounds)) : 0;
    $unifiedTotalRows = max($leftMax, $rightMax, 1) * 2;

    $leftGrid = $buildGridData($leftRounds, $unifiedTotalRows);
    $rightGrid = $buildGridData($rightRounds, $unifiedTotalRows);
    $rightDisplayNums = $rightGrid ? array_reverse($rightGrid['roundNums']) : [];

    $calcMidPct = function($grid) use ($unifiedTotalRows) {
        if (!$grid || empty($grid['roundNums'])) return 50;
        $lastNum = end($grid['roundNums']);
        $matches = $grid['data'][$lastNum];
        $T = $unifiedTotalRows;
        if (count($matches) >= 2) {
            $uPct = (($matches[0]['rowStart'] + $matches[0]['rowEnd'] - 2) / 2 / $T) * 100;
            $lPct = (($matches[1]['rowStart'] + $matches[1]['rowEnd'] - 2) / 2 / $T) * 100;
            return ($uPct + $lPct) / 2;
        }
        if (count($matches) === 1) {
            return (($matches[0]['rowStart'] + $matches[0]['rowEnd'] - 2) / 2 / $T) * 100;
        }
        return 50;
    };
    $centerPct = $calcMidPct($leftGrid);

    $activeEntryIds = $activeEntryIds ?? [];
    $activeEntryIntIds = array_map('intval', $activeEntryIds);
    $centerCompact = $leftGrid && $rightGrid ? true : false;
    $centerStyle = $centerCompact
        ? 'min-width: 200px; flex-shrink: 0; padding: 0 0.5rem;'
        : 'min-width: 220px; max-width: 280px; flex-shrink: 0; padding: 0 0.5rem;';
?>

<style>
    .bracket-tree-desktop { display: none; }
    .bracket-tree-mobile { display: flex; }
    @media (min-width: 1024px) {
        .bracket-tree-desktop { display: block !important; }
        .bracket-tree-mobile { display: none !important; }
    }
</style>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($filteredRounds) && !$thirdPlaceMatch): ?>
    <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
        <?php echo e(app()->getLocale() == 'id' ? 'Bagan pertandingan belum digenerate.' : 'The tournament bracket has not been generated yet.'); ?>

    </div>
<?php else: ?>


<div class="bracket-tree-desktop" style="width: 100%; overflow-x: auto; padding: 1.5rem 0; border-radius: 12px; background: transparent; border: none;">
    <div style="display: flex; align-items: stretch; gap: 0; min-height: calc(100vh - 180px); min-width: 800px;">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leftGrid): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $leftGrid['roundNums']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colIdx => $roundNum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                    <div style="height: 28px; text-align: center; flex-shrink: 0;">
                        <span style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; color: var(--primary);"><?php echo e($getRoundName($roundNum)); ?></span>
                    </div>
                    <div style="flex: 1; display: grid; grid-template-rows: repeat(<?php echo e($unifiedTotalRows); ?>, 1fr);">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $leftGrid['data'][$roundNum]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $match = $item['match'];
                                $home = $match['participants'][0] ?? null;
                                $away = $match['participants'][1] ?? null;
                                $homeId = $home['tournament_entry_id'] ?? 0;
                                $awayId = $away['tournament_entry_id'] ?? 0;
                                $isHighlighted = in_array($homeId, $activeEntryIntIds) || in_array($awayId, $activeEntryIntIds);
                            ?>
                            <div style="grid-row: <?php echo e($item['rowStart']); ?> / <?php echo e($item['rowEnd']); ?>; display: flex; align-items: center; padding: 3px 4px; position: relative; z-index: 1;">
                                <div class="bracket-match" style="width: 100%; border-radius: 8px; overflow: hidden; background: rgba(128,128,128,0.03); border: <?php echo e($isHighlighted ? '2px solid var(--primary); box-shadow: 0 0 12px rgba(16,185,129,0.3);' : '1px solid rgba(209,213,219,0.5);'); ?>">
                                    <?php echo $__env->make('_partials.bracket-match-card', ['match' => $match, 'home' => $home, 'away' => $away, 'activeEntryIds' => $activeEntryIntIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colIdx < $leftGrid['roundCount'] - 1): ?>
                    <?php
                        $outerMatches = $leftGrid['data'][$roundNum];
                        $pairCount = count($outerMatches) / 2;
                        $T = $unifiedTotalRows;
                        $stubW = 14;
                    ?>
                    <div style="width: 42px; flex-shrink: 0; position: relative;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = range(0, $pairCount - 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pairIdx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $upper = $outerMatches[$pairIdx * 2];
                                $lower = $outerMatches[$pairIdx * 2 + 1];
                                $uPct = (($upper['rowStart'] + $upper['rowEnd'] - 2) / 2 / $T) * 100;
                                $lPct = (($lower['rowStart'] + $lower['rowEnd'] - 2) / 2 / $T) * 100;
                                $mPct = ($uPct + $lPct) / 2;
                            ?>
                            <div style="position: absolute; left: 0; right: <?php echo e($stubW); ?>px; top: calc(<?php echo e($uPct); ?>% + <?php echo e(28 - 28 * $uPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                            <div style="position: absolute; left: 0; right: <?php echo e($stubW); ?>px; top: calc(<?php echo e($lPct); ?>% + <?php echo e(28 - 28 * $lPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                            <div style="position: absolute; right: <?php echo e($stubW); ?>px; top: calc(<?php echo e($uPct); ?>% + <?php echo e(28 - 28 * $uPct / 100); ?>px); height: calc(<?php echo e($lPct - $uPct); ?>% - <?php echo e(28 * ($lPct - $uPct) / 100); ?>px); width: 2px; background: rgba(20,184,166,0.3);"></div>
                            <div style="position: absolute; left: calc(100% - <?php echo e($stubW); ?>px); right: 0; top: calc(<?php echo e($mPct); ?>% + <?php echo e(28 - 28 * $mPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($finalRoundMatches)): ?>
            <?php
                $hasThirdPlace = !empty($thirdPlaceMatch);
                $offset = $hasThirdPlace ? 130 : 0;
            ?>
            <div style="width: 42px; flex-shrink: 0; position: relative;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasThirdPlace): ?>
                    <div style="position: absolute; left: 0; right: 21px; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                    <div style="position: absolute; left: 21px; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px - <?php echo e($offset); ?>px); height: <?php echo e($offset * 2); ?>px; width: 2px; background: rgba(20,184,166,0.3);"></div>
                    <div style="position: absolute; left: 21px; right: 0; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px - <?php echo e($offset); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                    <div style="position: absolute; left: 21px; right: 0; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px + <?php echo e($offset); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                <?php else: ?>
                    <div style="position: absolute; left: 0; right: 0; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div style="<?php echo e($centerStyle); ?> position: relative;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($finalRoundMatches)): ?>
                <?php
                    $fm = $finalRoundMatches[0] ?? null;
                    $fh = $fm['participants'][0] ?? null;
                    $fa = $fm['participants'][1] ?? null;
                    
                    $hasThirdPlace = !empty($thirdPlaceMatch);
                    $offset = $hasThirdPlace ? 130 : 0;
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fm): ?>
                    <div style="position: absolute; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px - <?php echo e($offset); ?>px); left: 0.5rem; right: 0.5rem; transform: translateY(-50%); z-index: 2;">
                        <div style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(20,184,166,0.2); color: var(--primary); text-align: center;">
                            <?php echo e($getRoundName($maxRound)); ?>

                        </div>
                        <div class="bracket-match" style="width: 100%; border-radius: 8px; overflow: hidden; background: rgba(128,128,128,0.03); margin-top: 0.75rem; box-shadow: 0 0 0 2px rgba(20,184,166,0.25); border: 1px solid rgba(209,213,219,0.5);">
                            <?php echo $__env->make('_partials.bracket-match-card', ['match' => $fm, 'home' => $fh, 'away' => $fa, 'activeEntryIds' => $activeEntryIntIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thirdPlaceMatch): ?>
                    <?php
                        $tp3h = $thirdPlaceMatch['participants'][0] ?? null;
                        $tp3a = $thirdPlaceMatch['participants'][1] ?? null;
                    ?>
                    <div style="position: absolute; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px + <?php echo e($offset); ?>px); left: 0.5rem; right: 0.5rem; transform: translateY(-50%); z-index: 2;">
                        <div style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(245,158,11,0.2); color: #f59e0b; text-align: center;">
                            PEREBUTAN JUARA 3
                        </div>
                        <div class="bracket-match" style="width: 100%; border-radius: 8px; overflow: hidden; background: rgba(128,128,128,0.03); margin-top: 0.75rem; border: 1px solid rgba(209,213,219,0.5);">
                            <?php echo $__env->make('_partials.bracket-match-card', ['match' => $thirdPlaceMatch, 'home' => $tp3h, 'away' => $tp3a, 'activeEntryIds' => $activeEntryIntIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($finalRoundMatches)): ?>
            <?php
                $hasThirdPlace = !empty($thirdPlaceMatch);
                $offset = $hasThirdPlace ? 130 : 0;
            ?>
            <div style="width: 42px; flex-shrink: 0; position: relative;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasThirdPlace): ?>
                    <div style="position: absolute; left: 21px; right: 0; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                    <div style="position: absolute; left: 21px; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px - <?php echo e($offset); ?>px); height: <?php echo e($offset * 2); ?>px; width: 2px; background: rgba(20,184,166,0.3);"></div>
                    <div style="position: absolute; left: 0; right: 21px; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px - <?php echo e($offset); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                    <div style="position: absolute; left: 0; right: 21px; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px + <?php echo e($offset); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                <?php else: ?>
                    <div style="position: absolute; left: 0; right: 0; top: calc(<?php echo e($centerPct); ?>% + <?php echo e(28 - 28 * $centerPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rightGrid): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rightDisplayNums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colIdx => $roundNum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                    <div style="height: 28px; text-align: center; flex-shrink: 0;">
                        <span style="font-weight: 800; font-size: 0.75rem; text-transform: uppercase; color: var(--primary);"><?php echo e($getRoundName($roundNum)); ?></span>
                    </div>
                    <div style="flex: 1; display: grid; grid-template-rows: repeat(<?php echo e($unifiedTotalRows); ?>, 1fr);">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rightGrid['data'][$roundNum]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $match = $item['match'];
                                $home = $match['participants'][0] ?? null;
                                $away = $match['participants'][1] ?? null;
                                $homeId = $home['tournament_entry_id'] ?? 0;
                                $awayId = $away['tournament_entry_id'] ?? 0;
                                $isHighlighted = in_array($homeId, $activeEntryIntIds) || in_array($awayId, $activeEntryIntIds);
                            ?>
                            <div style="grid-row: <?php echo e($item['rowStart']); ?> / <?php echo e($item['rowEnd']); ?>; display: flex; align-items: center; padding: 3px 4px; position: relative; z-index: 1;">
                                <div class="bracket-match" style="width: 100%; border-radius: 8px; overflow: hidden; background: rgba(128,128,128,0.03); border: <?php echo e($isHighlighted ? '2px solid var(--primary); box-shadow: 0 0 12px rgba(16,185,129,0.3);' : '1px solid rgba(209,213,219,0.5);'); ?>">
                                    <?php echo $__env->make('_partials.bracket-match-card', ['match' => $match, 'home' => $home, 'away' => $away, 'activeEntryIds' => $activeEntryIntIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($colIdx < count($rightDisplayNums) - 1): ?>
                    <?php
                        $outerRoundNum = $rightDisplayNums[$colIdx + 1];
                        $outerMatches = $rightGrid['data'][$outerRoundNum];
                        $pairCount = count($outerMatches) / 2;
                        $T = $unifiedTotalRows;
                        $stubW = 14;
                    ?>
                    <div style="width: 42px; flex-shrink: 0; position: relative;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = range(0, $pairCount - 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pairIdx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $upper = $outerMatches[$pairIdx * 2];
                                $lower = $outerMatches[$pairIdx * 2 + 1];
                                $uPct = (($upper['rowStart'] + $upper['rowEnd'] - 2) / 2 / $T) * 100;
                                $lPct = (($lower['rowStart'] + $lower['rowEnd'] - 2) / 2 / $T) * 100;
                                $mPct = ($uPct + $lPct) / 2;
                            ?>
                            <div style="position: absolute; left: <?php echo e($stubW); ?>px; right: 0; top: calc(<?php echo e($uPct); ?>% + <?php echo e(28 - 28 * $uPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                            <div style="position: absolute; left: <?php echo e($stubW); ?>px; right: 0; top: calc(<?php echo e($lPct); ?>% + <?php echo e(28 - 28 * $lPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                            <div style="position: absolute; left: <?php echo e($stubW); ?>px; top: calc(<?php echo e($uPct); ?>% + <?php echo e(28 - 28 * $uPct / 100); ?>px); height: calc(<?php echo e($lPct - $uPct); ?>% - <?php echo e(28 * ($lPct - $uPct) / 100); ?>px); width: 2px; background: rgba(20,184,166,0.3);"></div>
                            <div style="position: absolute; left: 0; right: calc(100% - <?php echo e($stubW); ?>px); top: calc(<?php echo e($mPct); ?>% + <?php echo e(28 - 28 * $mPct / 100); ?>px); height: 2px; background: rgba(20,184,166,0.3); transform: translateY(-50%);"></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div class="bracket-tree-mobile" style="max-width: 100%; overflow-x: auto; padding: 1.5rem; border-radius: 12px; background: rgba(128,128,128,0.03); border: 1px solid rgba(128,128,128,0.1); gap: 2.5rem; align-items: stretch;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $filteredRounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roundNum => $roundMatches): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="display: flex; flex-direction: column; gap: 0.5rem; min-width: 260px;">
            <div style="text-align: center; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(20,184,166,0.2); color: var(--primary);">
                <?php echo e($getRoundName($roundNum)); ?>

            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roundMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $home = $match['participants'][0] ?? null; $away = $match['participants'][1] ?? null; ?>
                <div class="bracket-match" style="width: 100%; border-radius: 8px; overflow: hidden; background: rgba(128,128,128,0.03); border: 1px solid rgba(209,213,219,0.5);">
                    <?php echo $__env->make('_partials.bracket-match-card', ['match' => $match, 'home' => $home, 'away' => $away, 'activeEntryIds' => $activeEntryIntIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thirdPlaceMatch): ?>
        <?php $tp3h = $thirdPlaceMatch['participants'][0] ?? null; $tp3a = $thirdPlaceMatch['participants'][1] ?? null; ?>
        <div style="min-width: 260px; display: flex; flex-direction: column; justify-content: center; gap: 0.5rem;">
            <div style="text-align: center; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(245,158,11,0.2); color: #f59e0b;">
                PEREBUTAN JUARA 3
            </div>
            <div class="bracket-match" style="width: 100%; border-radius: 8px; overflow: hidden; background: rgba(128,128,128,0.03); border: 1px solid rgba(209,213,219,0.5);">
                <?php echo $__env->make('_partials.bracket-match-card', ['match' => $thirdPlaceMatch, 'home' => $tp3h, 'away' => $tp3a, 'activeEntryIds' => $activeEntryIntIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\TurnamenPS\resources\views/_partials/bracket-tree.blade.php ENDPATH**/ ?>