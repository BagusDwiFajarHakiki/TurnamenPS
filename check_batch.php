<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$batch = \App\Models\EntryBatch::find(23);
echo "Batch 23 status: " . $batch->status . "\n";
echo "TournamentEntries for batch 23: " . \App\Models\TournamentEntry::where('entry_batch_id', 23)->count() . "\n";
