<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');

            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->onDelete('cascade');

            // Pivot fields
            $table->enum('status', [
                'assigned',
                'in_progress',
                'submitted',
                'graded',
                'late',
                'overdue',
                'missing',
            ])->default('assigned');
            
            $table->boolean('was_late')->default(false);
            $table->timestamp('due_date')->nullable();
            
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('penalty')->nullable();
            $table->integer('xp_earned')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->integer('retry_count')->default(0);

            $table->timestamps();
            $table->unique(['student_id','task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_tasks');
    }
};
