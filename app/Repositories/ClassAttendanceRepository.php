<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\ClassAttendance;
use App\Repositories\Contracts\ClassAttendanceRepositoryInterface;

class ClassAttendanceRepository implements ClassAttendanceRepositoryInterface
{
    public function getTodaySchedulesByTeacher(string $teacherId, string $dayOfWeek)
    {
        return Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get();
    }

    public function getAttendanceRecords(string $scheduleId, string $date)
    {
        return ClassAttendance::where('schedule_id', $scheduleId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id'); // Memudahkan pencarian data di array
    }

    public function updateOrCreateRecord(array $attributes, array $values)
    {
        return ClassAttendance::updateOrCreate($attributes, $values);
    }
}