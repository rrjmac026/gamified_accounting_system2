<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('xp_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->enum('type', ['earned', 'bonus', 'penalty', 'adjustment']);
            $table->enum('source', ['performance_task', 'task_completion', 'quiz_score', 'bonus_activity', 'manual']); // ✅ Added 'performance_task'
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description');
            $table->timestamp('processed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('xp_transactions');
    }
};