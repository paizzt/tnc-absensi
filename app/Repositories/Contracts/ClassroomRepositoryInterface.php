<?php

namespace App\Repositories\Contracts;

interface ClassroomRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 10);
    public function findById(string $id);
    public function create(array $data);
    public function update(string $id, array $data);
    public function delete(string $id);
}