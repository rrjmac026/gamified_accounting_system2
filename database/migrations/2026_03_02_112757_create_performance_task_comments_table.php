<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_task_id')->constrained('performance_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // sender (student OR instructor user)
            $table->foreignId('parent_id')->nullable()->constrained('performance_task_comments')->cascadeOnDelete(); // for replies
            $table->text('body');
            $table->boolean('is_read')->default(false);          // has the OTHER party read it?
            $table->enum('sender_role', ['student', 'instructor']);
            $table->integer('step')->nullable();                  // optional: tie comment to a specific step
            $table->softDeletes();
            $table->timestamps();

            $table->index(['performance_task_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_task_comments');
    }
};