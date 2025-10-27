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
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();

            // Link to user
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Notification content
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('link')->nullable();

            // Notification type: info, warning, success, error
            $table->string('type')->default('info');

            // Status & tracking
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
