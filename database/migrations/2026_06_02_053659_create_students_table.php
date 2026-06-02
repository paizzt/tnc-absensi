<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('school_id');
            $table->uuid('classroom_id');
            
            $table->string('nis', 20);
            $table->string('name');
            $table->enum('gender', ['L', 'P']); // Laki-laki / Perempuan
            $table->string('parent_phone', 20); // Nomor WA Gateway Orang Tua
            
            // String rahasia yang dirender jadi gambar QR (Mencegah pemalsuan)
            $table->string('qr_code_string')->unique(); 
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
            
            // Mencegah NIS ganda di dalam satu sekolah yang sama
            $table->unique(['school_id', 'nis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};