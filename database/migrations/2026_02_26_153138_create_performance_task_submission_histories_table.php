<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_task_submission_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('performance_task_submissions')->onDelete('cascade');
            $table->foreignId('task_id')->constrained('performance_tasks')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedTinyInteger('step');
            $table->unsignedSmallInteger('attempt_number');
            $table->longText('submission_data')->nullable();
            $table->string('status', 30)->default('in-progress');
            $table->decimal('score', 8, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedSmallInteger('error_count')->default(0);
            $table->boolean('is_late')->default(false);
            $table->timestamps();

            $table->index(['task_id', 'student_id', 'step'], 'pt_sub_hist_task_student_step_idx');
            $table->index(['submission_id', 'attempt_number'], 'pt_sub_hist_submission_attempt_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_task_submission_histories');
    }
};