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
        Schema::create('tournament_player_aggregates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->integer('total_entries')->default(0);
            $table->integer('total_matches_played')->default(0);
            $table->integer('total_goals_scored')->default(0);
            $table->integer('total_goals_conceded')->default(0);
            $table->integer('total_wins')->default(0);
            $table->integer('total_losses')->default(0);
            $table->integer('total_draws')->default(0);
            $table->integer('current_win_streak')->default(0);
            $table->integer('best_win_streak')->default(0);
            $table->integer('active_entries_count')->default(0);
            $table->integer('rank_position')->nullable();
            $table->timestamps();

            // Ensure unique combination of tournament and player
            $table->unique(['tournament_id', 'player_id'], 'tournament_player_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_player_aggregates');
    }
};
