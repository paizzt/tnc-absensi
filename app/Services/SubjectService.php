<?php

namespace App\Services;

use App\Repositories\Contracts\SubjectRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class SubjectService
{
    protected $subjectRepo;

    public function __construct(SubjectRepositoryInterface $subjectRepo)
    {
        $this->subjectRepo = $subjectRepo;
    }

    public function getSubjectsByCurrentSchool()
    {
        return $this->subjectRepo->getPaginatedBySchool(Auth::user()->school_id, 15);
    }

    public function createSubject(array $data)
    {
        $data['school_id'] = Auth::user()->school_id;
        return $this->subjectRepo->create($data);
    }

    public function updateSubject(string $id, array $data)
    {
        $subject = $this->subjectRepo->findById($id);
        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->subjectRepo->update($id, $data);
    }

    public function deleteSubject(string $id)
    {
        $subject = $this->subjectRepo->findById($id);
        if ($subject->school_id !== Auth::user()->school_id) {
            abort(403, 'Akses Ditolak.');
        }

        return $this->subjectRepo->delete($id);
    }
}