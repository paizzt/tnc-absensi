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
        Schema::table('students', function (Blueprint $table) {
            $table->string('parent_email')->nullable()->after('parent_phone');
        });

        Schema::table('school_settings', function (Blueprint $table) {
            $table->boolean('notify_in_email')->default(false)->after('notify_out');
            $table->boolean('notify_out_email')->default(false)->after('notify_in_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['notify_in_email', 'notify_out_email']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('parent_email');
        });
    }
};
