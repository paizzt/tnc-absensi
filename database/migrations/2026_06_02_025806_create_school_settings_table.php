<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('school_id')->unique();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            
            // Kustomisasi Aturan Sekolah
            $table->string('timezone')->default('Asia/Makassar');
            $table->time('time_in')->default('07:00:00');
            $table->time('time_late')->default('07:15:00');
            $table->time('time_out')->default('15:00:00');
            
            // Kategori Keterlambatan (dalam satuan menit)
            $table->integer('late_light_max')->default(15);
            $table->integer('late_medium_max')->default(30);
            
            // Pengaturan Notifikasi
            $table->boolean('notify_in')->default(true);
            $table->boolean('notify_out')->default(true);
            $table->boolean('notify_late')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};