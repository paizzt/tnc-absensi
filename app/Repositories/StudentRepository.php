<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 10, ?string $classroomId = null)
    {
        $query = Student::with('classroom')
            ->where('school_id', $schoolId);

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        return $query->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return Student::create($data);
    }
}