<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_task_submissions', function (Blueprint $table) {
            $table->id();

            // 🔹 Relationships
            $table->foreignId('task_id')->constrained('performance_tasks')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // 🔹 Submission details
            $table->integer('step')->default(1); // optional: default to first step
            $table->json('submission_data')->nullable();

            // 🔹 Evaluation data
            $table->string('status')->default('in-progress'); // e.g. in-progress, submitted, graded
            $table->integer('errors_count')->default(0);
            $table->integer('score')->default(0);
            $table->text('remarks')->nullable();

            // 🔹 Attempt tracking
            $table->integer('attempts')->default(0);

            $table->timestamps();

            // 🔹 Prevent duplicate submissions for the same step per student per task
            $table->unique(['task_id', 'student_id', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_task_submissions');
    }
};
