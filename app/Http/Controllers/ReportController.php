<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GateAttendance;
use App\Models\ClassAttendance;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
        } else {
            $selectedSchoolId = $user->school_id;
        }

        if ($user->hasRole('Guru')) {
            $homeroomClassIds = \App\Models\Classroom::where('teacher_id', $user->id)->pluck('id');
            $teachingClassIds = \App\Models\Schedule::where('teacher_id', $user->id)->pluck('classroom_id');
            $allowedClassIds = $homeroomClassIds->concat($teachingClassIds)->unique();
            
            $classrooms = Classroom::whereIn('id', $allowedClassIds)->orderBy('name')->get();

            $subjectIds = \App\Models\Schedule::where('teacher_id', $user->id)->pluck('subject_id')->unique();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name')->get();
        } else {
            $classrooms = Classroom::where('school_id', $selectedSchoolId)->orderBy('name')->get();
            $subjects = Subject::where('school_id', $selectedSchoolId)->orderBy('name')->get();
        }

        // Ambil data Kepala Sekolah untuk sekolah ini secara default
        $defaultKepsek = \App\Models\User::role('Kepala Sekolah')
            ->when($selectedSchoolId, function($q) use ($selectedSchoolId) {
                $q->where('school_id', $selectedSchoolId);
            })
            ->first();
        $defaultKepsekName = $defaultKepsek ? $defaultKepsek->name : '';
        $defaultKepsekNip = $defaultKepsek && isset($defaultKepsek->nip) ? $defaultKepsek->nip : '';
        $defaultExporterName = $user ? $user->name : '';
        $defaultExporterNip = $user && isset($user->nip) ? $user->nip : '';

        // Ambil kota default dari alamat sekolah atau Makassar
        $school = School::find($selectedSchoolId);
        $defaultCity = 'Makassar';
        if ($school && $school->address) {
            $parts = explode(',', $school->address);
            $lastPart = trim(end($parts));
            if (!empty($lastPart)) {
                $defaultCity = $lastPart;
            }
        }

        // Hitung jumlah siswa dengan email terdaftar
        $studentWithEmailCount = \App\Models\Student::where('school_id', $selectedSchoolId)
            ->whereNotNull('parent_email')
            ->where('parent_email', '!=', '')
            ->count();

        return view('admin.reports.index', compact(
            'schools', 'selectedSchoolId', 'classrooms', 'subjects', 
            'defaultKepsekName', 'defaultKepsekNip', 
            'defaultExporterName', 'defaultExporterNip', 
            'defaultCity', 'studentWithEmailCount'
        ));
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,csv'
        ]);

        $user = Auth::user();
        $schoolId = $request->input('school_id');
        if (!$schoolId && !$user->hasRole('Super Admin')) {
            $schoolId = $user->school_id;
        }
        if (!$schoolId) {
            $firstSchool = School::first();
            $schoolId = $firstSchool ? $firstSchool->id : null;
        }

        $classroomId = $request->input('classroom_id');
        $subjectId = $request->input('subject_id');

        $studentQuery = \App\Models\Student::with('classroom')->where('school_id', $schoolId);

        if ($user->hasRole('Guru')) {
            $homeroomClassIds = \App\Models\Classroom::where('teacher_id', $user->id)->pluck('id');
            $teachingClassIds = \App\Models\Schedule::where('teacher_id', $user->id)->pluck('classroom_id');
            $allowedClassIds = $homeroomClassIds->concat($teachingClassIds)->unique();

            if ($classroomId && $allowedClassIds->contains($classroomId)) {
                $studentQuery->where('classroom_id', $classroomId);
            } else {
                $studentQuery->whereIn('classroom_id', $allowedClassIds);
            }
        } else {
            if ($classroomId) {
                $studentQuery->where('classroom_id', $classroomId);
            }
        }

        $students = $studentQuery->orderBy('classroom_id')->orderBy('name')->get()->keyBy('id');
        $studentIds = $students->keys()->toArray();

        // 1. Ambil Gate Attendance (Absen Masuk & Pulang)
        $gateRecords = GateAttendance::where('school_id', $schoolId)
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->get()
            ->groupBy(function($item) {
                return $item->student_id . '_' . $item->date;
            });

        // 2. Ambil Class Attendance (Absen Permapel / Perjam)
        $classAttendanceQuery = ClassAttendance::with(['schedule.subject'])
            ->where('school_id', $schoolId)
            ->whereIn('student_id', $studentIds)
            ->whereBetween('date', [$request->start_date, $request->end_date]);

        if ($user->hasRole('Guru')) {
            $allowedSubjectIds = \App\Models\Schedule::where('teacher_id', $user->id)->pluck('subject_id')->unique();
            if ($subjectId && $allowedSubjectIds->contains($subjectId)) {
                $classAttendanceQuery->whereHas('schedule', function($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                });
            } else {
                $classAttendanceQuery->whereHas('schedule', function($q) use ($allowedSubjectIds) {
                    $q->whereIn('subject_id', $allowedSubjectIds);
                });
            }
        } else {
            if ($subjectId) {
                $classAttendanceQuery->whereHas('schedule', function($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                });
            }
        }

        $classRecords = $classAttendanceQuery->get()->groupBy(function($item) {
            return $item->student_id . '_' . $item->date;
        });

        // 3. Susun 9 Slot Jam Pelajaran berdasarkan SchoolSetting
        $setting = \App\Models\SchoolSetting::where('school_id', $schoolId)->first();
        $timeIn = $setting ? $setting->time_in : '07:00:00';
        $lessonDuration = $setting->lesson_duration ?? 45;
        $breakDuration = $setting->break_duration ?? 30;
        $breakAfter = $setting->break_after_lesson ?? 4;

        $timeSlots = [];
        $currentTime = \Carbon\Carbon::parse($timeIn);
        for ($i = 1; $i <= 9; $i++) {
            $start = $currentTime->format('H:i');
            $currentTime->addMinutes($lessonDuration);
            $end = $currentTime->format('H:i');
            $timeSlots[$i] = [
                'start' => $start,
                'end'   => $end,
            ];
            if ($i == $breakAfter) {
                $currentTime->addMinutes($breakDuration);
            }
        }

        // 4. Gabungkan data absensi
        $allKeys = $gateRecords->keys()->concat($classRecords->keys())->unique();

        $rows = [];
        foreach ($allKeys as $key) {
            [$sId, $date] = explode('_', $key, 2);
            $student = $students->get($sId);
            if (!$student) continue;

            $gateAtt = $gateRecords->get($key)?->first();
            $classAtts = $classRecords->get($key) ?? collect();

            // Status 9 jam pelajaran (1 s.d. 9)
            $periods = array_fill(1, 9, '-');
            $unassigned = [];

            foreach ($classAtts as $att) {
                $initial = substr(strtoupper($att->status), 0, 1);
                $assigned = false;

                if ($att->schedule && $att->schedule->start_time) {
                    $schedStart = substr($att->schedule->start_time, 0, 5);
                    for ($p = 1; $p <= 9; $p++) {
                        if ($timeSlots[$p]['start'] === $schedStart) {
                            $periods[$p] = $initial;
                            $assigned = true;
                            break;
                        }
                    }
                }

                if (!$assigned) {
                    $unassigned[] = [
                        'initial' => $initial,
                        'start_time' => $att->schedule ? $att->schedule->start_time : '00:00:00'
                    ];
                }
            }

            // Jika ada yang belum terpetakan ke slot jam exact, isi secara urut kronologis
            if (!empty($unassigned)) {
                usort($unassigned, fn($a, $b) => strcmp($a['start_time'], $b['start_time']));
                $idx = 0;
                for ($p = 1; $p <= 9 && $idx < count($unassigned); $p++) {
                    if ($periods[$p] === '-') {
                        $periods[$p] = $unassigned[$idx]['initial'];
                        $idx++;
                    }
                }
            }

            $scanIn = $gateAtt && $gateAtt->scan_in ? substr($gateAtt->scan_in, 0, 5) : '-';
            $scanOut = $gateAtt && $gateAtt->scan_out ? substr($gateAtt->scan_out, 0, 5) : '-';

            $rows[] = [
                'student_id'     => $student->id,
                'nis'            => $student->nis ?? '-',
                'name'           => $student->name ?? '-',
                'classroom'      => $student->classroom ? $student->classroom->name : '-',
                'scan_in'        => $scanIn,
                'periods'        => $periods,
                'scan_out'       => $scanOut,
                'date'           => date('d/m/Y', strtotime($date)),
                'raw_date'       => $date,
            ];
        }

        $rows = collect($rows)->sortBy(function($r) {
            return $r['raw_date'] . '_' . $r['classroom'] . '_' . $r['name'];
        })->values();

        $school = School::find($schoolId);

        // Penandatanganan: Kota & Tanggal Laporan
        $reportCity = $request->input('report_city') ?: ($defaultCity ?? 'Makassar');
        $reportDateInput = $request->input('report_date') ?: date('Y-m-d');
        $reportDateCarbon = \Carbon\Carbon::parse($reportDateInput)->locale('id');
        $formattedReportDate = $reportDateCarbon->translatedFormat('d F Y');

        // Ambil Data Kepala Sekolah
        $kepsekUser = \App\Models\User::role('Kepala Sekolah')
            ->when($schoolId, function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->first();

        $kepsekName = $request->input('kepsek_name') 
            ?: ($kepsekUser ? $kepsekUser->name : ($school->principal_name ?? '....................................'));
        $kepsekNip = $request->input('kepsek_nip') 
            ?: ($kepsekUser && isset($kepsekUser->nip) ? $kepsekUser->nip : '');

        // Penandatangan Kanan (Guru / Wali Kelas / Petugas)
        $signerTitle = $request->input('signer_title') ?: 'Guru';
        $exporterName = $request->input('exporter_name') 
            ?: ($user ? $user->name : '....................................');
        $exporterNip = $request->input('exporter_nip') 
            ?: ($user && isset($user->nip) ? $user->nip : '');

        // Simpan NIP ke profil pengguna jika kolom sudah tersedia di database
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'nip')) {
            try {
                if ($request->filled('kepsek_nip') && $kepsekUser && empty($kepsekUser->nip)) {
                    $kepsekUser->update(['nip' => $request->input('kepsek_nip')]);
                }
                if ($request->filled('exporter_nip') && $user && empty($user->nip)) {
                    $user->update(['nip' => $request->input('exporter_nip')]);
                }
            } catch (\Exception $e) {
                // Abaikan jika database belum siap
            }
        }

        if ($request->format == 'csv') {
            return $this->exportCsv($rows, $school, $kepsekName, $kepsekNip, $signerTitle, $exporterName, $exporterNip, $reportCity, $formattedReportDate);
        } else {
            return view('admin.reports.print', compact(
                'rows', 'request', 'school', 
                'kepsekName', 'kepsekNip', 
                'signerTitle', 'exporterName', 'exporterNip',
                'reportCity', 'formattedReportDate'
            ));
        }
    }

    private function exportCsv($rows, $school, $kepsekName, $kepsekNip, $signerTitle, $exporterName, $exporterNip, $reportCity, $formattedReportDate)
    {
        $fileName = 'Rekap_Absensi_' . str_replace(' ', '_', $school->name ?? 'Sekolah') . '_' . date('Ymd_His') . '.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($rows, $school, $kepsekName, $kepsekNip, $signerTitle, $exporterName, $exporterNip, $reportCity, $formattedReportDate) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['no', 'NIS', 'Nama', 'kelas', 'absen check in', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'absen check out', 'tanggal']);

            foreach ($rows as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row['nis'],
                    $row['name'],
                    $row['classroom'],
                    $row['scan_in'],
                    $row['periods'][1] ?? '-',
                    $row['periods'][2] ?? '-',
                    $row['periods'][3] ?? '-',
                    $row['periods'][4] ?? '-',
                    $row['periods'][5] ?? '-',
                    $row['periods'][6] ?? '-',
                    $row['periods'][7] ?? '-',
                    $row['periods'][8] ?? '-',
                    $row['periods'][9] ?? '-',
                    $row['scan_out'],
                    $row['date']
                ]);
            }

            // Tanda Tangan Bagian Bawah
            fputcsv($file, []);
            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $reportCity . ', ' . $formattedReportDate]);
            fputcsv($file, ['mengetahui,', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $signerTitle]);
            fputcsv($file, ['kepala sekolah', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);
            fputcsv($file, []);
            fputcsv($file, []);
            fputcsv($file, [$kepsekName, '', '', '', '', '', '', '', '', '', '', '', '', '', '', $exporterName]);
            fputcsv($file, ['NIP. ' . ($kepsekNip ?: '-'), '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'NIP. ' . ($exporterNip ?: '-')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sendWeeklyEmail(Request $request, \App\Services\WeeklyReportEmailService $service)
    {
        $schoolId = $request->input('school_id');
        $classroomId = $request->input('classroom_id');

        $user = Auth::user();
        if (!$schoolId && !$user->hasRole('Super Admin')) {
            $schoolId = $user->school_id;
        }

        $results = $service->sendAllWeeklyReports($schoolId, $classroomId);

        if ($results['sent'] > 0) {
            return back()->with('success', "Berhasil mengirim laporan kehadiran mingguan ke {$results['sent']} siswa/orang tua melalui email.");
        } elseif ($results['total'] == 0) {
            return back()->with('error', "Tidak ada siswa yang memiliki alamat email (parent_email) terdaftar pada kelas/sekolah yang dipilih.");
        } else {
            return back()->with('error', "Gagal mengirim email ke siswa. Pastikan konfigurasi SMTP/Gmail di .env sudah aktif.");
        }
    }

    public function sendCustomEmail(Request $request, \App\Services\WeeklyReportEmailService $service)
    {
        $request->validate([
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'school_id'    => 'nullable',
            'classroom_id' => 'nullable',
        ]);

        $schoolId = $request->input('school_id');
        $classroomId = $request->input('classroom_id') ?: null;
        $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();

        $user = Auth::user();
        if (!$schoolId && !$user->hasRole('Super Admin')) {
            $schoolId = $user->school_id;
        }

        $results = $service->sendAllWeeklyReports($schoolId, $classroomId, $startDate, $endDate);

        $formattedStart = $startDate->format('d/m/Y');
        $formattedEnd = $endDate->format('d/m/Y');
        $periodLabel = ($formattedStart === $formattedEnd) ? $formattedStart : "{$formattedStart} s.d. {$formattedEnd}";

        if ($results['sent'] > 0) {
            return back()->with('success', "Berhasil mengirim laporan kehadiran periode ({$periodLabel}) ke {$results['sent']} siswa/wali murid melalui email.");
        } elseif ($results['total'] == 0) {
            return back()->with('error', "Tidak ada siswa yang memiliki alamat email (parent_email) terdaftar pada kelas/sekolah yang dipilih.");
        } else {
            return back()->with('error', "Gagal mengirim email ke siswa. Pastikan konfigurasi SMTP/Gmail di .env sudah aktif.");
        }
    }
}