<?php

namespace App\Services;

use App\Models\Student;
use App\Models\GateAttendance;
use App\Models\ClassAttendance;
use App\Models\PermissionRequest;
use App\Models\SchoolSetting;
use App\Mail\WeeklyAttendanceReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WeeklyReportEmailService
{
    /**
     * Map nama hari Inggris ke Indonesia
     */
    protected $dayMap = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];

    /**
     * Membangun data rekapan mingguan untuk seorang siswa
     */
    public function buildWeeklyData(Student $student, Carbon $startDate, Carbon $endDate): array
    {
        $schoolId = $student->school_id;
        $setting = SchoolSetting::where('school_id', $schoolId)->first();
        $schoolDaysCount = $setting ? ($setting->school_days ?? 6) : 6;

        $startStr = $startDate->toDateString();
        $endStr = $endDate->toDateString();

        // Ambil Gate Attendance dalam rentang tanggal
        $gateRecords = GateAttendance::where('student_id', $student->id)
            ->whereBetween('date', [$startStr, $endStr])
            ->get()
            ->keyBy('date');

        // Ambil Class Attendance dalam rentang tanggal
        $classRecords = ClassAttendance::with(['schedule.subject'])
            ->where('student_id', $student->id)
            ->whereBetween('date', [$startStr, $endStr])
            ->get()
            ->groupBy('date');

        // Ambil Izin yang Disetujui
        $permissionRecords = PermissionRequest::where('student_id', $student->id)
            ->where('status', 'Disetujui')
            ->whereBetween('date', [$startStr, $endStr])
            ->get()
            ->keyBy('date');

        $period = CarbonPeriod::create($startDate, $endDate);

        $dailyRecords = [];
        $totalHadir = 0;
        $totalTerlambat = 0;
        $totalSakit = 0;
        $totalIzin = 0;
        $totalAlpha = 0;
        $activeSchoolDays = 0;

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $dayOfWeekEng = $date->format('l');
            $dayName = $this->dayMap[$dayOfWeekEng] ?? $dayOfWeekEng;
            $dayIndex = $date->dayOfWeekIso; // 1 (Senin) s.d. 7 (Minggu)

            // Cek apakah hari sekolah aktif
            $isSchoolDay = ($dayIndex <= $schoolDaysCount);
            if ($isSchoolDay) {
                $activeSchoolDays++;
            }

            $gate = $gateRecords->get($dateStr);
            $classes = $classRecords->get($dateStr) ?? collect();
            $permission = $permissionRecords->get($dateStr);

            $scanIn = $gate && $gate->scan_in ? substr($gate->scan_in, 0, 5) : '-';
            $scanOut = $gate && $gate->scan_out ? substr($gate->scan_out, 0, 5) : '-';

            // Ringkasan status kelas
            $classSummary = '-';
            if ($classes->isNotEmpty()) {
                $classStatuses = $classes->pluck('status')->unique();
                $classSummary = $classes->count() . ' Mapel (' . $classStatuses->implode(', ') . ')';
            }

            // Tentukan status hari ini
            $status = 'Libur';
            if ($gate) {
                if (strtolower($gate->status) === 'terlambat') {
                    $status = 'Terlambat';
                    $totalTerlambat++;
                    $totalHadir++;
                } else {
                    $status = 'Hadir';
                    $totalHadir++;
                }
            } elseif ($permission) {
                $status = $permission->type; // Sakit atau Izin
                if (strtolower($permission->type) === 'sakit') {
                    $totalSakit++;
                } else {
                    $totalIzin++;
                }
            } elseif ($classes->isNotEmpty()) {
                $hasHadir = $classes->contains(fn($c) => in_array(strtolower($c->status), ['hadir', 'terlambat']));
                $hasSakit = $classes->contains(fn($c) => strtolower($c->status) === 'sakit');
                $hasIzin = $classes->contains(fn($c) => strtolower($c->status) === 'izin');
                $hasAlpha = $classes->contains(fn($c) => strtolower($c->status) === 'alpha');

                if ($hasHadir) {
                    $status = 'Hadir';
                    $totalHadir++;
                } elseif ($hasSakit) {
                    $status = 'Sakit';
                    $totalSakit++;
                } elseif ($hasIzin) {
                    $status = 'Izin';
                    $totalIzin++;
                } elseif ($hasAlpha) {
                    $status = 'Alpha';
                    $totalAlpha++;
                } else {
                    $status = 'Hadir';
                    $totalHadir++;
                }
            } else {
                if (!$isSchoolDay) {
                    $status = 'Libur';
                } elseif ($date->isFuture()) {
                    $status = 'Belum Tiba';
                } elseif ($date->isToday()) {
                    $status = 'Belum Presensi';
                } else {
                    $status = 'Alpha';
                    $totalAlpha++;
                }
            }

            $dailyRecords[] = [
                'date'           => $dateStr,
                'formatted_date' => $date->format('d/m/Y'),
                'day_name'       => $dayName,
                'scan_in'        => $scanIn,
                'scan_out'       => $scanOut,
                'class_summary'  => $classSummary,
                'status'         => $status,
                'is_school_day'  => $isSchoolDay
            ];
        }

        $persentase = $activeSchoolDays > 0 ? round(($totalHadir / $activeSchoolDays) * 100) : 0;
        if ($persentase > 100) $persentase = 100;

        $summary = [
            'total_sekolah_hari' => $activeSchoolDays,
            'total_hadir'        => $totalHadir,
            'total_terlambat'    => $totalTerlambat,
            'total_sakit'        => $totalSakit,
            'total_izin'         => $totalIzin,
            'total_alpha'        => $totalAlpha,
            'persentase'         => $persentase,
        ];

        return [
            'summary'      => $summary,
            'dailyRecords' => $dailyRecords,
        ];
    }

    /**
     * Kirim email laporan mingguan ke satu siswa
     */
    public function sendToStudent(Student $student, Carbon $startDate, Carbon $endDate): bool
    {
        $email = trim($student->parent_email ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $data = $this->buildWeeklyData($student, $startDate, $endDate);

        Mail::to($email)->send(new WeeklyAttendanceReportMail(
            $student,
            $startDate->toDateString(),
            $endDate->toDateString(),
            $data['summary'],
            $data['dailyRecords']
        ));

        return true;
    }

    /**
     * Kirim email laporan mingguan ke seluruh siswa yang memiliki email
     */
    public function sendAllWeeklyReports(
        ?string $schoolId = null,
        ?string $classroomId = null,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null
    ): array {
        // Default rentang tanggal: Senin s.d. Sabtu minggu ini (atau 7 hari terakhir)
        if (!$startDate) {
            $startDate = Carbon::now()->startOfWeek();
        }
        if (!$endDate) {
            $endDate = Carbon::now()->startOfWeek()->addDays(5); // Sabtu
            if ($endDate->isFuture()) {
                $endDate = Carbon::now();
            }
        }

        $query = Student::with(['school', 'classroom'])
            ->whereNotNull('parent_email')
            ->where('parent_email', '!=', '')
            ->where('is_active', true);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        $students = $query->get();

        $results = [
            'total'   => $students->count(),
            'sent'    => 0,
            'failed'  => 0,
            'errors'  => [],
        ];

        foreach ($students as $student) {
            try {
                $sent = $this->sendToStudent($student, $startDate, $endDate);
                if ($sent) {
                    $results['sent']++;
                } else {
                    $results['failed']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Gagal mengirim ke {$student->name} ({$student->parent_email}): " . $e->getMessage();
                Log::error("Weekly Attendance Mail Error for {$student->name} ({$student->parent_email}): " . $e->getMessage());
            }
        }

        return $results;
    }
}
