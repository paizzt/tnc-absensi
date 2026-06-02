<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Repositories\Contracts\ScheduleRepositoryInterface;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 15)
    {
        return Schedule::with(['classroom', 'subject', 'teacher'])
            ->where('school_id', $schoolId)
            ->orderByRaw("FIELD(day_of_week, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('start_time')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return Schedule::create($data);
    }

    public function delete(string $id)
    {
        return Schedule::findOrFail($id)->delete();
    }
}