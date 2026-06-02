<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 10);
    public function create(array $data);
}