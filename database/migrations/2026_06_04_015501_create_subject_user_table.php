<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_user', function (Blueprint $table) {
            $table->id();
            // Sesuaikan foreignUuid jika ID tabel menggunakan UUID. Jika Integer, gunakan foreignId.
            $table->foreignUuid('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_user');
    }
};