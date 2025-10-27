<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_badges', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            
            $table->foreignId('badge_id')
                  ->constrained('badges')
                  ->onDelete('cascade');

            $table->timestamp('earned_at')->nullable();
            $table->timestamps();

            // no unique() → students can earn the same badge multiple times
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_badges');
    }
};
