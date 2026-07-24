<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tournament;
use App\Models\TournamentStage;
use App\Services\TournamentService;

class AutoGenerateBrackets extends Command
{
    protected $signature = 'app:auto-generate-brackets';
    protected $description = 'Automatically generate empty bracket after registration ends when minimum verified participants are met';

    public function handle()
    {
        $now = now();
        $service = app(TournamentService::class);

        $tournaments = Tournament::where('tournament_end', '>', $now)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereDoesntHave('stages')
            ->where(function ($query) use ($now) {
                $query->where('registration_end', '<=', $now)
                    ->orWhere('tournament_start', '<=', $now);
            })
            ->get();

        foreach ($tournaments as $tournament) {
            $this->processTournament($tournament, $service);
        }

        $completedTournaments = Tournament::where('tournament_end', '<=', $now)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereHas('stages')
            ->get();

        foreach ($completedTournaments as $tournament) {
            $tournament->update(['status' => 'completed']);
            $this->info("Tournament '{$tournament->name}' marked as completed.");
        }

        if ($tournaments->isEmpty() && $completedTournaments->isEmpty()) {
            $this->info("No tournaments ready for processing.");
        }
    }

    private function processTournament(Tournament $tournament, TournamentService $service): void
    {
        try {
            $this->info("Processing tournament: {$tournament->name} (ID: {$tournament->id})");

            $verifiedCount = $tournament->entries()
                ->where('status', 'verified')
                ->count();

            if ($verifiedCount < 2) {
                $this->warn("  Skipped: only {$verifiedCount} verified participant(s) (minimum 2 required).");
                return;
            }

            DB::transaction(function () use ($tournament, $service) {
                $stage = TournamentStage::create([
                    'tournament_id' => $tournament->id,
                    'name' => 'Sistem Gugur',
                    'stage_order' => 1,
                    'format' => 'single_elimination',
                    'source_type' => 'registration',
                    'status' => 'pending',
                ]);

                $service->generateBracket($stage);

                $tournament->update(['status' => 'ongoing']);
                
                // Assign verified slots automatically to the bracket
                $verifiedEntries = $tournament->entries()->where('status', 'verified')->get();
                foreach ($verifiedEntries as $entry) {
                    $service->assignSlot($entry);
                }
            });

            $this->info("  Empty bracket generated and slots assigned! ({$verifiedCount} verified slots)");

        } catch (\Exception $e) {
            $this->error("  Error processing tournament {$tournament->name}: {$e->getMessage()}");
        }
    }
}
