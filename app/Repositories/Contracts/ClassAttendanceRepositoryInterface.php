<?php

namespace App\Repositories\Contracts;

interface ClassAttendanceRepositoryInterface
{
    public function getTodaySchedulesByTeacher(string $teacherId, string $dayOfWeek);
    public function getAttendanceRecords(string $scheduleId, string $date);
    public function updateOrCreateRecord(array $attributes, array $values);
}