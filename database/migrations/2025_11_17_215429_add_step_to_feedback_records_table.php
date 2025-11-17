<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('feedback_records', function (Blueprint $table) {
            $table->integer('step')->nullable()->after('performance_task_id');
            
            // Add index for better query performance
            $table->index(['student_id', 'performance_task_id', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_records', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'performance_task_id', 'step']);
            $table->dropColumn('step');
        });
    }
};