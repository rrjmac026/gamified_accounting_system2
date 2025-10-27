<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('performance_task_answer_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_task_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('step');
            $table->json('correct_data')->nullable(); // JSON of correct answers
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_task_answer_sheets');
    }
};
