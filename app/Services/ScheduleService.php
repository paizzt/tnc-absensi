<?php

namespace App\Services;

use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ScheduleService
{
    protected $scheduleRepo;

    public function __construct(ScheduleRepositoryInterface $scheduleRepo)
    {
        $this->scheduleRepo = $scheduleRepo;
    }

    public function getSchedulesByCurrentSchool($requestedSchoolId = null)
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) {
            if (!$requestedSchoolId) return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return $this->scheduleRepo->getPaginatedBySchool($requestedSchoolId);
        }

        $schoolId = $user->school_id;
        if (!$schoolId) abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
        return $this->scheduleRepo->getPaginatedBySchool($schoolId);
    }

    public function createSchedule(array $data, $requestedSchoolId = null)
    {
        // Fungsi lama (Single Insert) tetap dipertahankan jika dibutuhkan
        $user = Auth::user();
        $schoolId = $user->hasRole('Super Admin') ? $requestedSchoolId : $user->school_id;
        if (!$schoolId) throw new Exception('Sekolah tidak valid.');
        
        $data['school_id'] = $schoolId;
        return $this->scheduleRepo->create($data);
    }

    // FUNGSI BARU: SIMPAN MATRIKS JADWAL (BULK INSERT)
    public function createBulkSchedule(array $data, $requestedSchoolId = null)
    {
        $user = Auth::user();
        $schoolId = $user->hasRole('Super Admin') ? $requestedSchoolId : $user->school_id;

        if (!$schoolId) throw new Exception('Sekolah tidak valid.');

        $classroomId = $data['classroom_id'];
        $rosterData = $data['roster'] ?? [];

        DB::beginTransaction();
        try {
            foreach ($rosterData as $day => $slots) {
                foreach ($slots as $slot) {
                    // Hanya proses jika Mapel dan Guru diisi (tidak kosong)
                    if (!empty($slot['subject_id']) && !empty($slot['teacher_id'])) {
                        
                        // Cek Bentrok
                        $collision = Schedule::where('school_id', $schoolId)
                            ->where('day_of_week', $day)
                            ->where('start_time', '<', $slot['end_time'])
                            ->where('end_time', '>', $slot['start_time'])
                            ->where(function($query) use ($slot, $classroomId) {
                                $query->where('teacher_id', $slot['teacher_id'])
                                      ->orWhere('classroom_id', $classroomId);
                            })
                            ->with(['teacher', 'classroom'])
                            ->first();

                        if ($collision) {
                            if ($collision->teacher_id == $slot['teacher_id']) {
                                throw new Exception("BENTROK: Guru {$collision->teacher->name} sudah mengajar di kelas {$collision->classroom->name} pada hari {$day} jam {$slot['start_time']}.");
                            }
                            if ($collision->classroom_id == $classroomId) {
                                throw new Exception("BENTROK: Kelas ini sudah memiliki jadwal lain pada hari {$day} jam {$slot['start_time']}.");
                            }
                        }

                        // Simpan ke database
                        $this->scheduleRepo->create([
                            'school_id' => $schoolId,
                            'classroom_id' => $classroomId,
                            'subject_id' => $slot['subject_id'],
                            'teacher_id' => $slot['teacher_id'],
                            'day_of_week' => $day,
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ]);
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e; // Lempar kembali pesan bentrok ke controller
        }
    }

    public function deleteSchedule(string $id)
    {
        return $this->scheduleRepo->delete($id);
    }
}