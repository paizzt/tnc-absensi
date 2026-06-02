<?php

namespace App\Repositories;

use App\Models\GateAttendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function findByStudentAndDate(string $studentId, string $date)
    {
        return GateAttendance::where('student_id', $studentId)
            ->where('date', $date)
            ->first();
    }

    public function create(array $data)
    {
        return GateAttendance::create($data);
    }

    public function update(string $id, array $data)
    {
        $attendance = GateAttendance::findOrFail($id);
        $attendance->update($data);
        return $attendance;
    }
}