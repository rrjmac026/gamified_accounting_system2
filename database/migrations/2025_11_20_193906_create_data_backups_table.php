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
        Schema::create('data_backups', function (Blueprint $table) {
            $table->id();
            $table->string('backup_name');
            $table->string('file_path')->nullable();
            $table->enum('backup_type', ['database', 'full'])->default('database');
            $table->bigInteger('file_size')->nullable()->comment('File size in bytes');
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('backup_date');
            $table->timestamp('retention_until')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('progress')->default(0)->comment('Progress percentage 0-100');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('status');
            $table->index('backup_type');
            $table->index('created_by');
            $table->index('backup_date');
            $table->index('retention_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_backups');
    }
};