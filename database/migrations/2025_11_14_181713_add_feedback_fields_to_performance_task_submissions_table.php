<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('performance_task_submissions', function (Blueprint $table) {
            $table->text('instructor_feedback')->nullable()->after('remarks');
            $table->timestamp('feedback_given_at')->nullable()->after('instructor_feedback');
            $table->boolean('needs_feedback')->default(false)->after('feedback_given_at');
        });
    }

    public function down()
    {
        Schema::table('performance_task_submissions', function (Blueprint $table) {
            $table->dropColumn(['instructor_feedback', 'feedback_given_at', 'needs_feedback']);
        });
    }
};