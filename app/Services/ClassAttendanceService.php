<?php

namespace App\Services;

use App\Repositories\Contracts\ClassAttendanceRepositoryInterface;
use App\Models\Student;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassAttendanceService
{
    protected $repo;

    public function __construct(ClassAttendanceRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getTeacherSchedulesForToday()
    {
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $todayStr = $days[Carbon::now()->format('l')];
        
        return $this->repo->getTodaySchedulesByTeacher(Auth::id(), $todayStr);
    }

    public function getStudentsForAttendance(string $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
        // Proteksi: Hanya guru yang bersangkutan yang bisa akses
        if ($schedule->teacher_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $date = Carbon::now()->format('Y-m-d');
        $students = Student::where('classroom_id', $schedule->classroom_id)
            ->where('is_active', true)
            ->orderBy('name')->get();
            
        $existingRecords = $this->repo->getAttendanceRecords($scheduleId, $date);

        return [
            'schedule' => $schedule,
            'students' => $students,
            'records' => $existingRecords,
            'date' => $date
        ];
    }

    public function saveAttendance(string $scheduleId, array $attendances)
    {
        $schoolId = Auth::user()->school_id;
        $date = Carbon::now()->format('Y-m-d');

        foreach ($attendances as $studentId => $status) {
            $this->repo->updateOrCreateRecord(
                [
                    'school_id' => $schoolId,
                    'schedule_id' => $scheduleId,
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                ]
            );
        }
    }
}