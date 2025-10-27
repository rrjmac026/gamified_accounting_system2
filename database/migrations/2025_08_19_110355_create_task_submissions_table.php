<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');

            // Submission details
            $table->json('submission_data');
            $table->string('file_path')->nullable();

            // Grading details
            $table->decimal('score', 5, 2)->nullable();
            $table->unsignedInteger('xp_earned')->default(0);
            $table->enum('status', ['pending', 'submitted', 'graded', 'late', 'incomplete'])
                ->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedInteger('attempt_number')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
