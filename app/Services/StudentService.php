<?php

namespace App\Services;

use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentService
{
    protected $studentRepo;

    public function __construct(StudentRepositoryInterface $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    public function getStudentsByCurrentSchool()
    {
        return $this->studentRepo->getPaginatedBySchool(Auth::user()->school_id, 15);
    }

    public function createStudent(array $data)
    {
        $data['school_id'] = Auth::user()->school_id;
        
        // Generate Token Rahasia untuk QR Code (Campuran UUID dan NIS)
        // Mencegah siswa menebak barcode teman
        $data['qr_code_string'] = 'SCANATTEND-' . Str::uuid()->toString();

        return $this->studentRepo->create($data);
    }
}