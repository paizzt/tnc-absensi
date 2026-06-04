<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Attendance;
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
        
        return view('teacher.attendances.show', compact('schedule', 'students'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        // Logika untuk menyimpan absensi manual di dalam kelas (Sakit/Izin/Alpa)
        // Akan kita kembangkan di tahap selanjutnya.
        return back()->with('success', 'Absensi kelas berhasil disimpan.');
    }
}