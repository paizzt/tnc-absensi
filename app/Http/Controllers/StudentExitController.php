<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentExit;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentExitController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        
        // Cari kelas di mana guru ini menjadi Wali Kelas
        $classroom = Classroom::where('teacher_id', $teacher->id)->first();
        
        if (!$classroom) {
            return redirect()->route('dashboard')->with('error', 'Anda belum ditugaskan sebagai Wali Kelas di kelas manapun.');
        }

        $students = Student::where('classroom_id', $classroom->id)->orderBy('name')->get();
        
        // Ambil data izin hari ini
        $exits = StudentExit::with('student')
            ->whereIn('student_id', $students->pluck('id'))
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.exits.index', compact('classroom', 'students', 'exits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:5|max:180' // Misal izin max 3 jam
        ]);

        StudentExit::create([
            'student_id' => $request->student_id,
            'approved_by' => Auth::id(),
            'reason' => $request->reason,
            'valid_until' => Carbon::now()->addMinutes($request->duration_minutes),
            'status' => 'Disetujui'
        ]);

        return back()->with('success', 'Izin keluar sementara berhasil diterbitkan. Siswa dapat menuju ke gerbang piket.');
    }
}