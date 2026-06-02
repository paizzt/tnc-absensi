<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id');
            $table->uuid('student_id');
            $table->date('date');
            $table->time('scan_in')->nullable();
            $table->time('scan_out')->nullable();
            $table->enum('status', ['Hadir', 'Terlambat', 'Bolos', 'Izin', 'Sakit', 'Alpha'])->default('Hadir');
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            
            // 1 Siswa hanya boleh punya 1 record per hari
            $table->unique(['student_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_attendances');
    }
};