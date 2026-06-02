<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id');
            $table->uuid('schedule_id');
            $table->uuid('student_id');
            $table->date('date');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'])->default('Hadir');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            // 1 Siswa hanya punya 1 record absensi per Jadwal di tanggal yang sama
            $table->unique(['schedule_id', 'student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_attendances');
    }
};