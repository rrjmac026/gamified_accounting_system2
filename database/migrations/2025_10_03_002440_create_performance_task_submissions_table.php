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

            $table->foreignId('task_id')->constrained('performance_tasks')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            $table->integer('step')->default(1);
            $table->unsignedBigInteger('exercise_id')->nullable(); // FK added separately below
            $table->json('submission_data')->nullable();

            $table->string('status')->default('in-progress');
            $table->integer('errors_count')->default(0);
            $table->decimal('score', 8, 2)->default(0); // fixed: was integer
            $table->text('remarks')->nullable();

            $table->integer('attempts')->default(0);

            $table->timestamps();

            $table->unique(['task_id', 'student_id', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_task_submissions');
    }
};