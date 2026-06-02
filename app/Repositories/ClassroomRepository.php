<?php

namespace App\Repositories;

use App\Models\Classroom;
use App\Repositories\Contracts\ClassroomRepositoryInterface;

class ClassroomRepository implements ClassroomRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 10)
    {
        return Classroom::where('school_id', $schoolId)
            ->orderBy('level', 'asc')
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    public function findById(string $id)
    {
        return Classroom::findOrFail($id);
    }

    public function create(array $data)
    {
        return Classroom::create($data);
    }

    public function update(string $id, array $data)
    {
        $classroom = $this->findById($id);
        $classroom->update($data);
        return $classroom;
    }

    public function delete(string $id)
    {
        $classroom = $this->findById($id);
        return $classroom->delete();
    }
}