<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ClassAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        
        // Translasi hari ini ke bahasa Indonesia untuk dicocokkan dengan Roster
        $hariInggris = strtolower(Carbon::now()->format('l'));
        $mapHari = [
            'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
            'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'
        ];
        $hariIni = $mapHari[$hariInggris];

        // Jika Super Admin, tampilkan semua jadwal hari ini (untuk pantauan)
        if ($teacher->hasRole('Super Admin')) {
            $schedules = Schedule::with(['classroom', 'subject', 'teacher'])
                ->where('day_of_week', $hariIni)
                ->orderBy('start_time')
                ->get();
        } else {
            // Jika Guru, HANYA tampilkan jadwal dia sendiri hari ini
            $schedules = Schedule::with(['classroom', 'subject'])
                ->where('teacher_id', $teacher->id)
                ->where('day_of_week', $hariIni)
                ->orderBy('start_time')
                ->get();
        }

        return view('teacher.attendances.index', compact('schedules', 'hariIni'));
    }

    public function show(Schedule $schedule)
    {
        // Pastikan guru hanya bisa membuka jadwalnya sendiri (kecuali Super Admin)
        if (!Auth::user()->hasRole('Super Admin') && $schedule->teacher_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        // Ambil daftar siswa di kelas tersebut
        $students = Student::where('classroom_id', $schedule->classroom_id)
            ->orderBy('name')
            ->get();

        // Cek apakah guru sudah melakukan absensi untuk sesi ini (opsional untuk pengembangan selanjutnya)
        $records = ClassAttendance::where('schedule_id', $schedule->id)
            ->where('date', Carbon::today()->toDateString())
            ->get()
            ->keyBy('student_id');
        
        return view('teacher.attendances.show', compact('schedule', 'students', 'records'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:Hadir,Sakit,Izin,Alpha,Dispensasi'
        ]);

        $date = Carbon::today()->toDateString();
        $schoolId = Auth::user()->school_id;

        foreach ($request->attendance as $studentId => $status) {
            ClassAttendance::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'student_id' => $studentId,
                    'date' => $date
                ],
                [
                    'school_id' => $schoolId,
                    'status' => $status
                ]
            );
        }

        return redirect()->route('teacher.attendances.index')->with('success', 'Absensi kelas berhasil disimpan.');
    }
}