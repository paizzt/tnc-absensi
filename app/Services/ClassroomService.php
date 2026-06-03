<?php

namespace App\Services;

use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

class ClassroomService
{
    protected $classroomRepo;

    public function __construct(ClassroomRepositoryInterface $classroomRepo)
    {
        $this->classroomRepo = $classroomRepo;
    }

    public function getClassroomsByCurrentSchool($requestedSchoolId = null)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            if (!$requestedSchoolId) {
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }
            return $this->classroomRepo->getPaginatedBySchool($requestedSchoolId, 15);
        }

        $schoolId = $user->school_id;
        if (!$schoolId) {
            abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
        }

        return $this->classroomRepo->getPaginatedBySchool($schoolId, 15);
    }

    public function createClassroom(array $data, $requestedSchoolId = null)
    {
        $user = Auth::user();
        $schoolId = $user->hasRole('Super Admin') ? $requestedSchoolId : $user->school_id;

        if (!$schoolId) {
            throw new Exception('Sekolah belum dipilih atau akun tidak memiliki sekolah.');
        }

        $data['school_id'] = $schoolId;
        return $this->classroomRepo->create($data);
    }

    public function updateClassroom(string $id, array $data)
    {
        $classroom = $this->classroomRepo->findById($id);
        
        // Pengecekan keamanan: Super admin bebas, admin sekolah hanya boleh edit kelasnya sendiri
        if (!Auth::user()->hasRole('Super Admin') && $classroom->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->classroomRepo->update($id, $data);
    }

    public function deleteClassroom(string $id)
    {
        $classroom = $this->classroomRepo->findById($id);
        
        if (!Auth::user()->hasRole('Super Admin') && $classroom->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->classroomRepo->delete($id);
    }
}