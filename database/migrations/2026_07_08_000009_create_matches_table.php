<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_stage_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('tournament_groups')->nullOnDelete();
            $table->integer('round_number')->default(1);
            $table->integer('match_order')->default(1);
            $table->string('bracket_position')->nullable(); // E.g., "1.1" for Round 1, Match 1
            $table->foreignId('next_match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->foreignId('loser_next_match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->boolean('is_bye')->default(false);
            $table->string('status')->default('pending'); // pending, ready, scheduled, ongoing, completed, walkover, disputed, cancelled
            $table->foreignId('ps_unit_id')->nullable()->constrained('ps_units')->nullOnDelete();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->integer('best_of')->default(1);
            $table->boolean('decided_by_penalty')->default(false);
            $table->integer('penalty_score_home')->nullable();
            $table->integer('penalty_score_away')->nullable();
            $table->string('result_proof_path')->nullable();
            $table->foreignId('no_show_entry_id')->nullable()->constrained('tournament_entries')->nullOnDelete();
            $table->string('walkover_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
