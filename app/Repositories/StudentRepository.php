<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 10)
    {
        // Eager load classroom agar lebih cepat saat me-render tabel
        return Student::with('classroom')
            ->where('school_id', $schoolId)
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return Student::create($data);
    }
}