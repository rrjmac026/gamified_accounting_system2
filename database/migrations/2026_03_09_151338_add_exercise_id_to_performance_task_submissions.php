<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('performance_task_submissions', function (Blueprint $table) {
            $table->foreign('exercise_id')
                ->references('id')
                ->on('performance_task_exercises')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('performance_task_submissions', function (Blueprint $table) {
            $table->dropForeign(['exercise_id']);
        });
    }
};