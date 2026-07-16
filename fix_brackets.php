<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$matches = \App\Models\GameMatch::whereIn('status', ['completed', 'walkover'])
    ->whereNull('finished_at')
    ->get();

foreach ($matches as $match) {
    $match->resolveResultAndAdvance();
    echo "Resolved match #{$match->id} ({$match->status})\n";
}

echo "Done! Resolved " . $matches->count() . " matches.\n";
