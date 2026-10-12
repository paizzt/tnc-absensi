<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Pengiriman Laporan Absensi Mingguan ke Email Siswa
// Dijalankan otomatis tiap hari Sabtu pukul 18:00 WITA (akhir pekan sekolah)
Schedule::command('attendance:send-weekly-reports')
    ->weeklyOn(6, '18:00')
    ->timezone('Asia/Makassar')
    ->appendOutputTo(storage_path('logs/weekly-attendance-reports.log'));

