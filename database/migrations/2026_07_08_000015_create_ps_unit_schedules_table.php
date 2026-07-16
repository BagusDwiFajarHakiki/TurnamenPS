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
        Schema::create('ps_unit_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ps_unit_id')->constrained('ps_units')->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->dateTime('booked_from');
            $table->dateTime('booked_until')->nullable();
            $table->string('status')->default('booked'); // booked, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ps_unit_schedules');
    }
};
