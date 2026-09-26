<?php

namespace App\Repositories;

use App\Models\PermissionRequest;
use App\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function create(array $data)
    {
        return PermissionRequest::create($data);
    }

    public function getPaginatedBySchool(string $schoolId, ?string $classroomId = null, int $perPage = 10)
    {
        // Mengambil data urut dari yang terbaru dan belum diproses (Menunggu)
        $query = PermissionRequest::with(['student.classroom'])
            ->where('school_id', $schoolId);

        if ($classroomId) {
            $query->whereHas('student', function($q) use ($classroomId) {
                $q->where('classroom_id', $classroomId);
            });
        }

        return $query->orderByRaw("FIELD(status, 'Menunggu', 'Disetujui', 'Ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(string $id)
    {
        return PermissionRequest::with('student')->findOrFail($id);
    }

    public function update(string $id, array $data)
    {
        $req = $this->findById($id);
        $req->update($data);
        return $req;
    }
}