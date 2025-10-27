<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instructor_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['instructor_id', 'subject_id']);
        });
    }

    public function down()
    {
        Schema::table('instructor_subject', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropForeign(['subject_id']);
        });
        
        Schema::dropIfExists('instructor_subject');
    }
};
