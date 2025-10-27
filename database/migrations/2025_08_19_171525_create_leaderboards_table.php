<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('rank_position')->nullable();
            $table->integer('total_xp')->default(0);
            $table->decimal('total_score', 8, 2)->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->enum('period_type', ['weekly', 'monthly', 'semester', 'overall'])->default('overall');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};
