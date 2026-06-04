<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_exits', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('approved_by')->constrained('users')->cascadeOnDelete(); // ID Wali Kelas
            $table->string('reason');
            $table->dateTime('valid_until'); // Batas waktu izin
            $table->dateTime('scanned_out_at')->nullable(); // Waktu scan di gerbang (keluar)
            $table->dateTime('scanned_in_at')->nullable(); // Waktu scan di gerbang (kembali)
            $table->enum('status', ['Disetujui', 'Keluar', 'Kembali', 'Terlambat'])->default('Disetujui');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_exits');
    }
};