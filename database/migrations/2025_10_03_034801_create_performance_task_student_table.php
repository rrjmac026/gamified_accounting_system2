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
        Schema::create('performance_task_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_task_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');

            // Task assignment & submission info
            $table->enum('status', ['assigned', 'in_progress', 'submitted', 'graded', 'returned'])
                  ->default('assigned');
            $table->json('completed_steps')->nullable();
            $table->json('submission_data')->nullable();
            $table->integer('score')->nullable();
            $table->integer('xp_earned')->default(0);
            $table->text('feedback')->nullable();
            $table->integer('attempts')->default(0);

            // Important: for deadlines and grading
            $table->timestamp('due_date')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();

            $table->timestamps();

            // Ensure unique assignment per student per task
            $table->unique(['performance_task_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_task_student');
    }
};
