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
        Schema::create('gamification_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('min_passing_score')->default(70); // in percentage
            $table->integer('xp_per_task')->default(100); // default XP per task
            $table->integer('level_up_threshold')->default(1000); // XP to level up
            $table->integer('late_penalty')->default(0); // XP penalty
            $table->integer('bonus_for_perfect')->default(0); // bonus XP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_settings');
    }
};
