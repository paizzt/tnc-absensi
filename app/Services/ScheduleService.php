<?php

namespace App\Services;

use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Exception;

class ScheduleService
{
    protected $scheduleRepo;

    public function __construct(ScheduleRepositoryInterface $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
    }

    public function getSchedulesByCurrentSchool()
    {
        $schoolId = Auth::user()->school_id;
        
        // Pelindung jika akun belum dikaitkan ke sekolah
        if (!$schoolId) {
            abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun. Hubungi Super Admin.');
        }

        return $this->scheduleRepo->getPaginatedBySchool($schoolId);
    }

    public function createSchedule(array $data)
    {
        $schoolId = Auth::user()->school_id;
        
        if (!$schoolId) {
            throw new Exception('Akun Anda belum ditugaskan ke sekolah manapun.');
        }
        
        $data['school_id'] = $schoolId;

        if ($data['start_time'] >= $data['end_time']) {
            throw new Exception('Jam mulai harus lebih awal dari jam selesai.');
        }

        // COLLISION DETECTION LOGIC
        $collision = Schedule::where('school_id', $schoolId)
            ->where('day_of_week', $data['day_of_week'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->where(function($query) use ($data) {
                $query->where('teacher_id', $data['teacher_id'])
                      ->orWhere('classroom_id', $data['classroom_id']);
            })
            ->with(['teacher', 'classroom'])
            ->first();

        if ($collision) {
            if ($collision->teacher_id == $data['teacher_id']) {
                throw new Exception("BENTROK: Guru {$collision->teacher->name} sudah mengajar di kelas {$collision->classroom->name} pada jam tersebut.");
            }
            if ($collision->classroom_id == $data['classroom_id']) {
                throw new Exception("BENTROK: Kelas {$collision->classroom->name} sudah memiliki jadwal pelajaran lain pada jam tersebut.");
            }
        }

        return $this->scheduleRepo->create($data);
    }

    public function deleteSchedule(string $id)
    {
        return $this->scheduleRepo->delete($id);
    }
}