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
        Schema::create('ps_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // E.g., PS-01
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('console_type')->default('PS3'); // PS3, PS4, Playbox, etc.
            $table->integer('controller_count')->default(2);
            $table->string('status')->default('active'); // active, maintenance, inactive
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ps_units');
    }
};
