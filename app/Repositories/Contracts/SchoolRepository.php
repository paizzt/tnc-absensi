<?php

namespace App\Repositories;

use App\Models\School;
use App\Repositories\Contracts\SchoolRepositoryInterface;

class SchoolRepository implements SchoolRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10)
    {
        return School::latest()->paginate($perPage);
    }

    public function findById(string $id)
    {
        return School::findOrFail($id);
    }

    public function create(array $data)
    {
        return School::create($data);
    }

    public function update(string $id, array $data)
    {
        $school = $this->findById($id);
        $school->update($data);
        return $school;
    }

    public function delete(string $id)
    {
        $school = $this->findById($id);
        return $school->delete();
    }
}