<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('department')->nullable();
            $table->string('specialization')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        
        // Drop any pivot tables that reference instructors
        Schema::dropIfExists('instructor_subject');
        Schema::dropIfExists('instructor_section');
        
        // Now safe to drop instructors table
        Schema::dropIfExists('instructors');
        
        Schema::enableForeignKeyConstraints();
    }
};
