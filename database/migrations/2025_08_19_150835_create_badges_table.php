<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Badge name
            $table->text('description')->nullable(); // Optional badge description
            $table->string('icon_path')->nullable(); // Path to badge icon/image
            $table->string('criteria')->nullable(); // Store multiple conditions as JSON
            $table->integer('xp_threshold')->default(0); // XP required to earn badge
            $table->boolean('is_active')->default(true); // Whether badge is active
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
