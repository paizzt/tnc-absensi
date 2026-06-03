<?php

namespace App\Services;

use App\Repositories\Contracts\SubjectRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Exception;

class SubjectService
{
    protected $subjectRepo;

    public function __construct(SubjectRepositoryInterface $subjectRepo)
    {
        $this->subjectRepo = $subjectRepo;
    }

    public function getSubjectsByCurrentSchool($requestedSchoolId = null)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            if (!$requestedSchoolId) {
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }
            return $this->subjectRepo->getPaginatedBySchool($requestedSchoolId, 15);
        }

        $schoolId = $user->school_id;
        if (!$schoolId) {
            abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
        }

        return $this->subjectRepo->getPaginatedBySchool($schoolId, 15);
    }

    public function createSubject(array $data, $requestedSchoolId = null)
    {
        $user = Auth::user();
        $schoolId = $user->hasRole('Super Admin') ? $requestedSchoolId : $user->school_id;

        if (!$schoolId) {
            throw new Exception('Sekolah belum dipilih atau akun tidak memiliki sekolah.');
        }

        $data['school_id'] = $schoolId;
        return $this->subjectRepo->create($data);
    }

    public function updateSubject(string $id, array $data)
    {
        $subject = $this->subjectRepo->findById($id);
        
        if (!Auth::user()->hasRole('Super Admin') && $subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->subjectRepo->update($id, $data);
    }

    public function deleteSubject(string $id)
    {
        $subject = $this->subjectRepo->findById($id);
        
        if (!Auth::user()->hasRole('Super Admin') && $subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->subjectRepo->delete($id);
    }
}