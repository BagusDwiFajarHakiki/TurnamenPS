<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tournament;
use App\Models\TournamentStage;
use App\Models\TournamentEntry;
use App\Models\Player;
use App\Models\Club;
use App\Models\PsUnit;
use App\Models\GameMatch;
use App\Models\MatchParticipant;
use App\Models\TournamentPlayerAggregate;
use App\Models\EntryBatch;
use App\Services\TournamentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    public function test_bracket_generation_and_bye_advancement()
    {
        // 1. Create a tournament and stage
        $tournament = Tournament::create([
            'name' => 'Tournament Test',
            'slug' => 'tournament-test',
            'game_title' => 'eFootball',
            'price_per_slot' => 10000,
            'max_slot_per_player' => 5,
            'max_entries' => 32,
            'entry_expiry_hours' => 24,
            'no_show_deadline_minutes' => 10,
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(2),
            'tournament_start' => now()->addDays(3),
            'tournament_end' => now()->addDays(5),
            'status' => 'registration',
        ]);

        $stage = TournamentStage::create([
            'tournament_id' => $tournament->id,
            'name' => 'Penyisihan',
            'stage_order' => 1,
            'format' => 'single_elimination',
            'status' => 'pending',
            'source_type' => 'registration',
        ]);

        // 2. Create 5 players and check-in entries (which triggers a size-8 bracket with 3 BYEs)
        $players = [];
        $entries = [];
        for ($i = 1; $i <= 5; $i++) {
            $player = Player::create([
                'name' => "Player {$i}",
                'username' => "player{$i}",
                'login_code_plain_hint' => '123456',
                'is_active' => true,
            ]);
            $players[] = $player;

            $batch = EntryBatch::create([
                'tournament_id' => $tournament->id,
                'player_id' => $player->id,
                'slot_count' => 1,
                'total_price' => 10000,
                'status' => 'verified',
            ]);

            $entry = TournamentEntry::create([
                'tournament_id' => $tournament->id,
                'player_id' => $player->id,
                'entry_batch_id' => $batch->id,
                'entry_label' => "{$player->name} Slot",
                'entry_number' => 1,
                'status' => 'checked_in',
            ]);
            $entries[] = $entry;
        }

        // 3. Generate the bracket
        $service = new TournamentService();
        $service->generateBracket($stage);

        // Assert stage status is now ongoing
        $this->assertEquals('ongoing', $stage->fresh()->status);

        // Assert 7 matches were generated (4 in R1, 2 in R2, 1 in R3)
        $matches = GameMatch::where('tournament_stage_id', $stage->id)->get();
        $this->assertCount(7, $matches);

        // Assert 3 matches are BYEs in Round 1
        $r1Byes = GameMatch::where('tournament_stage_id', $stage->id)
            ->where('round_number', 1)
            ->where('is_bye', true)
            ->get();
        
        $this->assertCount(3, $r1Byes);

        // Assert Round 2 has participants advanced from the BYE matches
        $r2Matches = GameMatch::where('tournament_stage_id', $stage->id)
            ->where('round_number', 2)
            ->get();
        
        // At least 3 sides in Round 2 should be filled due to BYEs
        $r2ParticipantsCount = MatchParticipant::whereIn('match_id', $r2Matches->pluck('id'))->count();
        $this->assertGreaterThanOrEqual(3, $r2ParticipantsCount);
    }

    public function test_fifo_queue_allocation_and_match_completion()
    {
        $tournament = Tournament::create([
            'name' => 'Tournament Test 2',
            'slug' => 'tournament-test-2',
            'game_title' => 'eFootball',
            'price_per_slot' => 10000,
            'max_slot_per_player' => 5,
            'max_entries' => 32,
            'entry_expiry_hours' => 24,
            'no_show_deadline_minutes' => 10,
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(2),
            'tournament_start' => now()->addDays(3),
            'tournament_end' => now()->addDays(5),
            'status' => 'registration',
        ]);

        $stage = TournamentStage::create([
            'tournament_id' => $tournament->id,
            'name' => 'Penyisihan',
            'stage_order' => 1,
            'format' => 'single_elimination',
            'status' => 'pending',
            'source_type' => 'registration',
        ]);

        // Create 2 players, verified entries
        $p1 = Player::create(['name' => 'A', 'username' => 'playerA', 'login_code_plain_hint' => '111111']);
        $p2 = Player::create(['name' => 'B', 'username' => 'playerB', 'login_code_plain_hint' => '222222']);

        $b1 = EntryBatch::create(['tournament_id' => $tournament->id, 'player_id' => $p1->id, 'slot_count' => 1, 'total_price' => 10000, 'status' => 'verified']);
        $b2 = EntryBatch::create(['tournament_id' => $tournament->id, 'player_id' => $p2->id, 'slot_count' => 1, 'total_price' => 10000, 'status' => 'verified']);

        $e1 = TournamentEntry::create(['tournament_id' => $tournament->id, 'player_id' => $p1->id, 'entry_batch_id' => $b1->id, 'entry_label' => 'A', 'entry_number' => 1, 'status' => 'checked_in']);
        $e2 = TournamentEntry::create(['tournament_id' => $tournament->id, 'player_id' => $p2->id, 'entry_batch_id' => $b2->id, 'entry_label' => 'B', 'entry_number' => 1, 'status' => 'checked_in']);

        // Create a PS Unit
        $unit = PsUnit::create([
            'code' => 'PS-TEST',
            'name' => 'PS Test',
            'location' => 'TV Test',
            'console_type' => 'PS4',
            'status' => 'active',
        ]);

        // Generate bracket (size 2, 1 match)
        $service = new TournamentService();
        $service->generateBracket($stage);

        $match = GameMatch::where('tournament_stage_id', $stage->id)->first();
        $this->assertEquals('ready', $match->status);

        // Process queue -> should assign PS-TEST unit to this match
        $service->processQueue();
        
        $match = $match->fresh();
        $this->assertEquals('scheduled', $match->status);
        $this->assertEquals($unit->id, $match->ps_unit_id);

        // Complete the match with score 4-2 (Home A wins)
        $homePart = $match->participants->where('side', 'home')->first();
        $awayPart = $match->participants->where('side', 'away')->first();

        // Update score in participants
        $homePart->update(['goals_scored' => 4]);
        $awayPart->update(['goals_scored' => 2]);

        // Trigger completed status update which fires event listeners
        $match->update(['status' => 'completed']);

        // Assert player A won
        $this->assertTrue($homePart->fresh()->is_winner);
        $this->assertFalse($awayPart->fresh()->is_winner);

        // Check that Player aggregate statistics were populated correctly
        $aggA = TournamentPlayerAggregate::where('tournament_id', $tournament->id)->where('player_id', $p1->id)->first();
        $aggB = TournamentPlayerAggregate::where('tournament_id', $tournament->id)->where('player_id', $p2->id)->first();

        $this->assertNotNull($aggA);
        $this->assertEquals(1, $aggA->total_wins);
        $this->assertEquals(4, $aggA->total_goals_scored);
        $this->assertEquals(1, $aggA->current_win_streak);

        $this->assertNotNull($aggB);
        $this->assertEquals(1, $aggB->total_losses);
        $this->assertEquals(2, $aggB->total_goals_scored);
        $this->assertEquals(0, $aggB->current_win_streak);
    }

    public function test_player_checkin_rules()
    {
        $startDate = \Illuminate\Support\Carbon::parse('2026-07-12 10:00:00');

        $tournament = Tournament::create([
            'name' => 'Checkin Test',
            'slug' => 'checkin-test',
            'game_title' => 'eFootball',
            'price_per_slot' => 10000,
            'max_slot_per_player' => 5,
            'max_entries' => 32,
            'entry_expiry_hours' => 24,
            'no_show_deadline_minutes' => 10,
            'check_in_open_minutes_before' => 120, // 2 hours
            'registration_start' => $startDate->copy()->subDays(5),
            'registration_end' => $startDate->copy()->subDays(1),
            'tournament_start' => $startDate,
            'tournament_end' => $startDate->copy()->addDays(2),
            'status' => 'registration',
        ]);

        $player = Player::create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'login_code_plain_hint' => '123456',
            'is_active' => true,
        ]);

        $batch = EntryBatch::create([
            'tournament_id' => $tournament->id,
            'player_id' => $player->id,
            'slot_count' => 1,
            'total_price' => 10000,
            'status' => 'pending',
        ]);

        $batch->update(['status' => 'verified']);

        // Verifying batch should create entry with 'verified' status
        $entry = TournamentEntry::where('entry_batch_id', $batch->id)->first();
        $this->assertNotNull($entry);
        $this->assertEquals('verified', $entry->status);

        // Instantiate livewire component
        $component = new \App\Livewire\Player\Dashboard();
        $component->player = $player;

        // 1. Try checking in before the check-in window opens -> should fail
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-07-12 07:59:00'));
        $component->checkInAllSlots();
        $this->assertEquals('verified', $entry->fresh()->status);
        $this->assertTrue(session()->has('checkin_error'));

        // 2. Try checking in on match day but after start time -> should fail
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-07-12 10:01:00'));
        $component->checkInAllSlots();
        $this->assertEquals('verified', $entry->fresh()->status);

        // 3. Try checking in on match day during open window -> should succeed
        $entry->update(['status' => 'verified']);
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-07-12 09:00:00'));
        $component->checkInAllSlots();
        $this->assertEquals('checked_in', $entry->fresh()->status);
    }

    public function test_clean_expired_batches()
    {
        $baseTime = \Illuminate\Support\Carbon::parse('2026-07-12 12:00:00');
        $this->travelTo($baseTime);

        $tournament = Tournament::create([
            'name' => 'Expiry Test',
            'slug' => 'expiry-test',
            'game_title' => 'eFootball',
            'price_per_slot' => 10000,
            'max_slot_per_player' => 5,
            'max_entries' => 32,
            'entry_expiry_hours' => 2, // 2 hours
            'no_show_deadline_minutes' => 10,
            'registration_start' => $baseTime->copy()->subDays(5),
            'registration_end' => $baseTime->copy()->addDays(1),
            'tournament_start' => $baseTime->copy()->addDays(2),
            'tournament_end' => $baseTime->copy()->addDays(4),
            'status' => 'registration',
        ]);

        $player = Player::create([
            'name' => 'Expiry Player',
            'username' => 'expiryplayer',
            'login_code_plain_hint' => '123456',
            'is_active' => true,
        ]);

        // Create Batch 1 at baseTime (12:00:00)
        $expiredBatch = EntryBatch::create([
            'tournament_id' => $tournament->id,
            'player_id' => $player->id,
            'slot_count' => 1,
            'total_price' => 10000,
            'status' => 'pending',
        ]);

        // Travel 3 hours to 15:00:00
        $this->travelTo($baseTime->copy()->addHours(3));

        // Create Batch 2 at 15:00:00
        $validBatch = EntryBatch::create([
            'tournament_id' => $tournament->id,
            'player_id' => $player->id,
            'slot_count' => 1,
            'total_price' => 10000,
            'status' => 'pending',
        ]);

        // Travel 1 hour to 16:00:00 (Batch 1 is 4 hours old, Batch 2 is 1 hour old)
        $this->travelTo($baseTime->copy()->addHours(4));

        // Run the command
        $this->artisan('app:clean-expired-batches')
            ->assertExitCode(0);

        // Assert Batch 1 is expired/rejected
        $this->assertEquals('rejected', $expiredBatch->fresh()->status);
        $this->assertStringContainsString('telah habis', $expiredBatch->fresh()->rejection_reason);

        // Assert Batch 2 is still pending
        $this->assertEquals('pending', $validBatch->fresh()->status);
    }

    public function test_walkover_handling_and_corrections()
    {
        // 1. Create a tournament and stage
        $tournament = Tournament::create([
            'name' => 'Tournament Test WO',
            'slug' => 'tournament-test-wo',
            'game_title' => 'eFootball',
            'price_per_slot' => 10000,
            'max_slot_per_player' => 5,
            'max_entries' => 32,
            'entry_expiry_hours' => 24,
            'no_show_deadline_minutes' => 10,
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(2),
            'tournament_start' => now()->addDays(3),
            'tournament_end' => now()->addDays(5),
            'status' => 'registration',
        ]);

        $stage = TournamentStage::create([
            'tournament_id' => $tournament->id,
            'name' => 'Penyisihan WO',
            'stage_order' => 1,
            'format' => 'single_elimination',
            'status' => 'pending',
            'source_type' => 'registration',
        ]);

        // Create 2 players
        $players = [];
        $entries = [];
        for ($i = 1; $i <= 2; $i++) {
            $player = Player::create([
                'name' => "Player WO {$i}",
                'username' => "playerwo{$i}",
                'login_code_plain_hint' => '123456',
                'is_active' => true,
            ]);
            $players[] = $player;

            $batch = EntryBatch::create([
                'tournament_id' => $tournament->id,
                'player_id' => $player->id,
                'slot_count' => 1,
                'total_price' => 10000,
                'status' => 'verified',
            ]);

            $entry = TournamentEntry::create([
                'tournament_id' => $tournament->id,
                'player_id' => $player->id,
                'entry_batch_id' => $batch->id,
                'entry_label' => "{$player->name} Slot",
                'entry_number' => 1,
                'status' => 'checked_in',
            ]);
            $entries[] = $entry;
        }

        // Generate the bracket
        $service = new TournamentService();
        $service->generateBracket($stage);

        // Find the match
        $match = GameMatch::where('tournament_stage_id', $stage->id)->first();
        $this->assertNotNull($match);

        $homePart = $match->participants->where('side', 'home')->first();
        $awayPart = $match->participants->where('side', 'away')->first();

        $this->assertNotNull($homePart);
        $this->assertNotNull($awayPart);

        // Set home player as no-show
        $match->update([
            'status' => 'walkover',
            'no_show_entry_id' => $homePart->tournament_entry_id,
            'walkover_reason' => 'No show home player',
        ]);

        // Refresh and assert
        $homePart = $homePart->fresh();
        $awayPart = $awayPart->fresh();

        $this->assertEquals(0, $homePart->goals_scored);
        $this->assertFalse($homePart->is_winner);
        $this->assertEquals(3, $awayPart->goals_scored);
        $this->assertTrue($awayPart->is_winner);

        // Assert walkover count of home player is 1
        $this->assertEquals(1, $homePart->entry->fresh()->walkover_count);
        $this->assertEquals(0, $awayPart->entry->fresh()->walkover_count);

        // Correct/swap: Set away player as no-show instead
        $match->update([
            'no_show_entry_id' => $awayPart->tournament_entry_id,
            'walkover_reason' => 'No show away player correction',
        ]);

        // Refresh and assert swapped status
        $homePart = $homePart->fresh();
        $awayPart = $awayPart->fresh();

        $this->assertEquals(3, $homePart->goals_scored);
        $this->assertTrue($homePart->is_winner);
        $this->assertEquals(0, $awayPart->goals_scored);
        $this->assertFalse($awayPart->is_winner);

        // Assert walkover count of home player is reverted to 0, away is now 1
        $this->assertEquals(0, $homePart->entry->fresh()->walkover_count);
        $this->assertEquals(1, $awayPart->entry->fresh()->walkover_count);
    }

    public function test_score_update_propagates_winner_change()
    {
        // 1. Create a tournament and stage
        $tournament = Tournament::create([
            'name' => 'Tournament Test Propagate',
            'slug' => 'tournament-test-propagate',
            'game_title' => 'eFootball',
            'price_per_slot' => 10000,
            'max_slot_per_player' => 5,
            'max_entries' => 32,
            'entry_expiry_hours' => 24,
            'no_show_deadline_minutes' => 10,
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(2),
            'tournament_start' => now()->addDays(3),
            'tournament_end' => now()->addDays(5),
            'status' => 'registration',
        ]);

        $stage = TournamentStage::create([
            'tournament_id' => $tournament->id,
            'name' => 'Penyisihan Propagate',
            'stage_order' => 1,
            'format' => 'single_elimination',
            'status' => 'pending',
            'source_type' => 'registration',
        ]);

        // Create 4 players (gives a size 4 bracket, 2 matches in round 1, 1 match in round 2)
        $players = [];
        $entries = [];
        for ($i = 1; $i <= 4; $i++) {
            $player = Player::create([
                'name' => "Player P {$i}",
                'username' => "playerp{$i}",
                'login_code_plain_hint' => '123456',
                'is_active' => true,
            ]);
            $players[] = $player;

            $batch = EntryBatch::create([
                'tournament_id' => $tournament->id,
                'player_id' => $player->id,
                'slot_count' => 1,
                'total_price' => 10000,
                'status' => 'verified',
            ]);

            $entry = TournamentEntry::create([
                'tournament_id' => $tournament->id,
                'player_id' => $player->id,
                'entry_batch_id' => $batch->id,
                'entry_label' => "{$player->name} Slot",
                'entry_number' => 1,
                'status' => 'checked_in',
            ]);
            $entries[] = $entry;
        }

        // Generate the bracket
        $service = new TournamentService();
        $service->generateBracket($stage);

        // Find Round 1 Match 1
        $match1 = GameMatch::where('tournament_stage_id', $stage->id)
            ->where('round_number', 1)
            ->where('match_order', 1)
            ->first();
        $this->assertNotNull($match1);
        $this->assertNotNull($match1->next_match_id);

        $homePart = $match1->participants->where('side', 'home')->first();
        $awayPart = $match1->participants->where('side', 'away')->first();

        // Let's set Home player as winner (e.g. 2-1)
        $homePart->update(['goals_scored' => 2]);
        $awayPart->update(['goals_scored' => 1]);
        $match1->update(['status' => 'completed']);

        // Assert Home player is winner and advanced to next match
        $homePart = $homePart->fresh();
        $awayPart = $awayPart->fresh();
        $this->assertTrue($homePart->is_winner);
        $this->assertFalse($awayPart->is_winner);

        $nextMatch = GameMatch::with('participants')->find($match1->next_match_id);
        // Next match order % 2 === 1 since match_order is 1, so winner goes to home side of next match
        $nextHomePart = $nextMatch->participants->where('side', 'home')->first();
        $this->assertNotNull($nextHomePart);
        $this->assertEquals($homePart->tournament_entry_id, $nextHomePart->tournament_entry_id);

        // Now, update score: Away player wins instead (e.g. 2-3)
        $homePart->update(['goals_scored' => 2]);
        $awayPart->update(['goals_scored' => 3]);
        // Trigger resolution
        $match1->resolveResultAndAdvance();

        // Assert Away player is now the winner and advanced to next match instead
        $homePart = $homePart->fresh();
        $awayPart = $awayPart->fresh();
        $this->assertFalse($homePart->is_winner);
        $this->assertTrue($awayPart->is_winner);

        $nextMatchReloaded = GameMatch::with('participants')->find($match1->next_match_id);
        $nextHomePartReloaded = $nextMatchReloaded->participants->where('side', 'home')->first();
        $this->assertNotNull($nextHomePartReloaded);
        $this->assertEquals($awayPart->tournament_entry_id, $nextHomePartReloaded->tournament_entry_id);
    }
}
