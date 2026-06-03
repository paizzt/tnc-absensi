<?php

namespace App\Repositories;

use App\Models\Subject;
use App\Repositories\Contracts\SubjectRepositoryInterface;


class SubjectRepository implements SubjectRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 10)
    {
        return Subject::where('school_id', $schoolId)
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    public function findById(string $id)
    {
        return Subject::findOrFail($id);
    }

    public function create(array $data)
    {
        return Subject::create($data);
    }

    public function update(string $id, array $data)
    {
        $subject = $this->findById($id);
        $subject->update($data);
        return $subject;
    }

    public function delete(string $id)
    {
        $subject = $this->findById($id);
        return $subject->delete();
    }
}