<?php

namespace App\Repositories\Contracts;

interface ScheduleRepositoryInterface
{
    public function getPaginatedBySchool(string $schoolId, int $perPage = 15);
    public function create(array $data);
    public function delete(string $id);
}