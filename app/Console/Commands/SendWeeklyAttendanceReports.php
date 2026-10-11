<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeeklyReportEmailService;
use Carbon\Carbon;

class SendWeeklyAttendanceReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-weekly-reports 
                            {--school_id= : Batasi pengiriman hanya untuk ID Sekolah tertentu} 
                            {--classroom_id= : Batasi pengiriman hanya untuk ID Kelas tertentu}
                            {--start_date= : Tanggal awal periode (format: YYYY-MM-DD)}
                            {--end_date= : Tanggal akhir periode (format: YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim laporan kehadiran mingguan secara otomatis ke email (Gmail) siswa/orang tua';

    /**
     * Execute the console command.
     */
    public function handle(WeeklyReportEmailService $service)
    {
        $schoolId = $this->option('school_id');
        $classroomId = $this->option('classroom_id');
        $startDateStr = $this->option('start_date');
        $endDateStr = $this->option('end_date');

        $startDate = $startDateStr ? Carbon::parse($startDateStr) : Carbon::now()->startOfWeek();
        $endDate = $endDateStr ? Carbon::parse($endDateStr) : Carbon::now()->startOfWeek()->addDays(5); // Sabtu
        if (!$endDateStr && $endDate->isFuture()) {
            $endDate = Carbon::now();
        }

        $this->info("=== MEMULAI PENGIRIMAN LAPORAN KEHADIRAN MINGGUAN ===");
        $this->line("Periode: {$startDate->format('d/m/Y')} s.d. {$endDate->format('d/m/Y')}");
        if ($schoolId) $this->line("Filter Sekolah ID: {$schoolId}");
        if ($classroomId) $this->line("Filter Kelas ID: {$classroomId}");

        $results = $service->sendAllWeeklyReports($schoolId, $classroomId, $startDate, $endDate);

        $this->newLine();
        $this->info("=== HASIL PENGIRIMAN ===");
        $this->line("Total Siswa Ber-email : {$results['total']}");
        $this->line("Berhasil Terkirim     : {$results['sent']}");
        $this->line("Gagal/Dilewati        : {$results['failed']}");

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->warn("Detail Kendala:");
            foreach ($results['errors'] as $err) {
                $this->error(" - " . $err);
            }
        }

        return Command::SUCCESS;
    }
}
