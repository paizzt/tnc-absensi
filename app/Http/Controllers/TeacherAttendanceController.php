<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClassAttendanceService;

class TeacherAttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(ClassAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $schedules = $this->attendanceService->getTeacherSchedulesForToday();
        return view('teacher.attendances.index', compact('schedules'));
    }

    public function show($scheduleId)
    {
        $data = $this->attendanceService->getStudentsForAttendance($scheduleId);
        return view('teacher.attendances.show', $data);
    }

    public function store(Request $request, $scheduleId)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:Hadir,Sakit,Izin,Alpha,Dispensasi'
        ]);

        $this->attendanceService->saveAttendance($scheduleId, $request->attendance);
        
        return redirect()->route('teacher.attendances.index')
            ->with('success', 'Data absensi kelas berhasil disimpan.');
    }
}