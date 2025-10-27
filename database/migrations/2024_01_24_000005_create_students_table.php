<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_number')->unique();
            $table->foreignId('course_id')->constrained()->onDelete('restrict');
            $table->string('year_level');
            $table->string('section_id')->nullable();
            $table->unsignedInteger('total_xp')->default(0);
            $table->unsignedInteger('current_level')->default(1);
            $table->decimal('performance_rating', 5, 2)->default(0.00);
            $table->boolean('hide_from_leaderboard')->default(false); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};