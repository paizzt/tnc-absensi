<?php

namespace App\Repositories\Contracts;

interface AttendanceRepositoryInterface
{
    public function findByStudentAndDate(string $studentId, string $date);
    public function create(array $data);
    public function update(string $id, array $data);
}