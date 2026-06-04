<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->integer('lesson_duration')->default(45)->after('time_out');
            $table->integer('break_duration')->default(30)->after('lesson_duration');
            $table->integer('break_after_lesson')->default(4)->after('break_duration');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['lesson_duration', 'break_duration', 'break_after_lesson']);
        });
    }
};