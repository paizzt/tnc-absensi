<?php

namespace App\Services;

use App\Repositories\Contracts\SchoolRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class SchoolService
{
    protected $schoolRepo;

    public function __construct(SchoolRepositoryInterface $schoolRepo)
    {
        $this->schoolRepo = $schoolRepo;
    }

    public function getAllSchools()
    {
        return $this->schoolRepo->getAllPaginated(10);
    }

    public function createSchoolWithDefaultSettings(array $data)
    {
        DB::beginTransaction();
        try {
            // 1. Buat Data Sekolah
            $school = $this->schoolRepo->create($data);
            
            // 2. Buat Pengaturan Default Otomatis
            $school->settings()->create([
                'timezone' => 'Asia/Makassar',
                'time_in' => '07:00:00',
                'time_late' => '07:15:00',
                'time_out' => '15:00:00',
                'late_light_max' => 15,
                'late_medium_max' => 30,
            ]);

            DB::commit();
            return $school;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function getSchoolById(string $id)
    {
        return $this->schoolRepo->findById($id);
    }

    public function updateSchool(string $id, array $data)
    {
        return $this->schoolRepo->update($id, $data);
    }

    public function deleteSchool(string $id)
    {
        return $this->schoolRepo->delete($id);
    }
}