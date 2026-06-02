<?php

namespace App\Services;

use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ClassroomService
{
    protected $classroomRepo;

    public function __construct(ClassroomRepositoryInterface $classroomRepo)
    {
        $this->classroomRepo = $classroomRepo;
    }

    public function getClassroomsByCurrentSchool()
    {
        return $this->classroomRepo->getPaginatedBySchool(Auth::user()->school_id, 15);
    }

    public function createClassroom(array $data)
    {
        // Kunci data ke sekolah Admin yang sedang login
        $data['school_id'] = Auth::user()->school_id;
        return $this->classroomRepo->create($data);
    }

    public function updateClassroom(string $id, array $data)
    {
        // Pastikan kelas yang diedit benar-benar milik sekolahnya (Security)
        $classroom = $this->classroomRepo->findById($id);
        if ($classroom->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->classroomRepo->update($id, $data);
    }

    public function deleteClassroom(string $id)
    {
        $classroom = $this->classroomRepo->findById($id);
        if ($classroom->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->classroomRepo->delete($id);
    }
}