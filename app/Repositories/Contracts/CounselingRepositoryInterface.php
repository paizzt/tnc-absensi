<?php

namespace App\Repositories\Contracts;

interface CounselingRepositoryInterface
{
    public function getStudentsWithBadAttendance(string $schoolId);
    public function createWarningLetter(array $data);
}