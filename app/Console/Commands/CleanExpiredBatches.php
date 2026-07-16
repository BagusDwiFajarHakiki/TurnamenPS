<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EntryBatch;

class CleanExpiredBatches extends Command
{
    protected $signature = 'app:clean-expired-batches';
    protected $description = 'Expire pending entry batches that have exceeded the tournament payment deadline';

    public function handle()
    {
        $this->info('Checking for expired entry batches...');
        
        $pendingBatches = EntryBatch::where('status', 'pending')
            ->with('tournament')
            ->get();
            
        $count = 0;
        foreach ($pendingBatches as $batch) {
            $tournament = $batch->tournament;
            if (!$tournament) continue;
            
            $expiryHours = $tournament->entry_expiry_hours ?? 24;
            $expiryTime = $batch->created_at->addHours($expiryHours);
            
            if (now()->gt($expiryTime)) {
                $batch->update([
                    'status' => 'rejected',
                    'rejection_reason' => "Batas waktu pembayaran/verifikasi ({$expiryHours} jam) telah habis."
                ]);
                $count++;
            }
        }
        
        $this->info("Successfully expired {$count} pending entry batches.");
    }
}
