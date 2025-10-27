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
        // Main sections table
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_code')->unique(); // unique identifier (e.g., BSIT-3A)
            $table->string('name'); // e.g., "Section A"
            $table->unsignedBigInteger('course_id')->nullable(); // link to course
            $table->integer('capacity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });

        // Pivot: instructors assigned to sections
        Schema::create('instructor_section', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('section_id');
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');

            $table->unique(['instructor_id', 'section_id']); // avoid duplicates
        });

        // Pivot: students assigned to sections
        Schema::create('section_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('section_id');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');

            $table->unique(['student_id', 'section_id']); // avoid duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_student');
        Schema::dropIfExists('instructor_section');
        Schema::dropIfExists('sections');
    }
};
