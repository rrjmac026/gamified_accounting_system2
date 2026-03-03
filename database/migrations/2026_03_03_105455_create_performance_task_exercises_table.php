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
        Schema::create('performance_task_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_task_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('step'); // 1–10
            $table->string('title');             // Exercise title e.g. "Exercise 1"
            $table->text('description')->nullable();
            $table->json('correct_data');        // The answer sheet data
            $table->integer('order')->default(0); // For ordering exercises within a step
            $table->timestamps();

            $table->index(['performance_task_id', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_task_exercises');
    }
};
