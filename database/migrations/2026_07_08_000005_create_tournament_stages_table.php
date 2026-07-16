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
        Schema::create('tournament_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // E.g., "Sistem Gugur", "Penyisihan", "Final"
            $table->integer('stage_order')->default(1);
            $table->string('format')->default('single_elimination'); // single_elimination, double_elimination, round_robin, group_stage
            $table->string('status')->default('pending'); // pending, ongoing, completed
            $table->string('source_type')->default('registration'); // registration, previous_stage_winners, previous_stage_top_n
            $table->json('config')->nullable(); // Additional format settings (e.g., seeding, group counts)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_stages');
    }
};
