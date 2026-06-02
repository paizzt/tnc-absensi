<?php

namespace App\Repositories\Contracts;

interface PermissionRepositoryInterface
{
    public function create(array $data);
    public function getPaginatedBySchool(string $schoolId, int $perPage = 10);
    public function findById(string $id);
    public function update(string $id, array $data);
}