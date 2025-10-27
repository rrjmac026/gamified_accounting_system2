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
        Schema::create('performance_task_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_task_id')->constrained()->onDelete('cascade');
            $table->integer('step_number');
            $table->string('title')->nullable();
            $table->string('sheet_name')->nullable();
            $table->longText('template_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_task_steps');
    }
};
