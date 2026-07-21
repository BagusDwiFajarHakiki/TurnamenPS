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
        Schema::create('match_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('tournament_entry_id')->nullable()->constrained('tournament_entries')->cascadeOnDelete();
            $table->string('side'); // home, away
            $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->integer('goals_scored')->default(0);
            $table->boolean('is_winner')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_participants');
    }
};
