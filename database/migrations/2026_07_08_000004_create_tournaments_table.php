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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('game_title');
            $table->decimal('price_per_slot', 10, 2)->default(0.00);
            $table->integer('max_slot_per_player')->nullable();
            $table->integer('max_entries')->nullable();
            $table->integer('entry_expiry_hours')->default(24);
            $table->text('payment_info')->nullable();
            $table->text('rules_content')->nullable();
            $table->integer('no_show_deadline_minutes')->default(10);
            $table->dateTime('registration_start')->nullable();
            $table->dateTime('registration_end')->nullable();
            $table->dateTime('tournament_start')->nullable();
            $table->dateTime('tournament_end')->nullable();
            $table->string('status')->default('draft'); // draft, registration_open, registration_closed, ongoing, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
