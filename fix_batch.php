<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$batch = \App\Models\EntryBatch::find(23);
if ($batch) {
    // Set to pending so the user can verify it again
    $batch->updateQuietly(['status' => 'pending']);
    echo "Fixed batch 23. Status is now pending.\n";
}
