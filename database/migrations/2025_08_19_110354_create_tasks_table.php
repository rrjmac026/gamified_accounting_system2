<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Core Task fields
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['assignment', 'exercise', 'quiz', 'project', 'question']);
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->integer('max_score');
            $table->integer('xp_reward');
            $table->timestamp('due_date')->nullable();
            $table->unsignedInteger('retry_limit')->default(1);
            $table->unsignedInteger('late_penalty')->nullable();
            $table->text('instructions');
            $table->enum('status', ['draft', 'pending', 'active', 'completed', 'archived'])->default('pending');
            $table->boolean('is_active')->default(true); // consider removing if redundant
            $table->boolean('auto_grade')->default(false);
            $table->string('attachment')->nullable();
            $table->dateTime('late_until')->nullable();

            // Question-specific fields
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay', 'calculation'])->nullable();
            $table->text('correct_answer')->nullable();
            $table->integer('points')->nullable();
            $table->integer('order_index')->nullable();
            $table->json('options')->nullable();

            $table->timestamps();

            // Indexes for better performance
            $table->index(['type', 'parent_task_id']);
            $table->index(['instructor_id', 'subject_id']);
            $table->index(['is_active', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
