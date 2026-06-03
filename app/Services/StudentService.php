<?php

namespace App\Services;

use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class StudentService
{
    protected $studentRepo;

    public function __construct(StudentRepositoryInterface $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    public function getStudentsByCurrentSchool($requestedSchoolId = null)
    {
        $user = Auth::user();

        // Mode Dewa Super Admin
        if ($user->hasRole('Super Admin')) {
            if (!$requestedSchoolId) {
                return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }
            return $this->studentRepo->getPaginatedBySchool($requestedSchoolId, 15);
        }

        // Mode Admin Sekolah / Petugas Piket
        $schoolId = $user->school_id;
        if (!$schoolId) {
            abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
        }

        return $this->studentRepo->getPaginatedBySchool($schoolId, 15);
    }

    public function createStudent(array $data, $requestedSchoolId = null)
    {
        $user = Auth::user();
        $schoolId = $user->hasRole('Super Admin') ? $requestedSchoolId : $user->school_id;

        if (!$schoolId) throw new Exception('Sekolah tidak valid atau belum dipilih.');

        $data['school_id'] = $schoolId;
        // Generate UUID Rahasia untuk QR Code Siswa
        $data['qr_code_string'] = 'SCANATTEND-' . Str::uuid()->toString();

        return $this->studentRepo->create($data);
    }

    // FUNGSI IMPORT DATA DARI CSV
    public function importStudentsCsv($file, $schoolId)
    {
        // Buka file CSV
        $handle = fopen($file->getRealPath(), 'r');
        
        // Lewati baris pertama karena itu adalah baris Judul (Header Template)
        fgetcsv($handle, 1000, ',');
        
        $successCount = 0;
        $errors = [];
        $rowNum = 1;

        // Baca file baris demi baris
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;
            
            // Pastikan kolom lengkap sesuai template (5 Kolom)
            if (count($data) < 5) continue;

            $nis = trim($data[0]);
            $name = trim($data[1]);
            $className = trim($data[2]);
            $gender = strtoupper(trim($data[3]));
            $phone = trim($data[4]);

            // 1. Validasi jika ada Kolom Kosong
            if (empty($nis) || empty($name) || empty($className) || empty($gender) || empty($phone)) {
                $errors[] = "Baris $rowNum: Ada kolom yang kosong (Dilewati).";
                continue;
            }

            // 2. Validasi format Jenis Kelamin
            if (!in_array($gender, ['L', 'P'])) {
                $errors[] = "Baris $rowNum ($name): Jenis kelamin harus diisi L atau P (Dilewati).";
                continue;
            }

            // 3. Validasi NIS Ganda (Mencegah Duplicate Entry)
            $exists = Student::where('school_id', $schoolId)->where('nis', $nis)->exists();
            if ($exists) {
                $errors[] = "Baris $rowNum: NIS '$nis' sudah terdaftar di sistem (Dilewati).";
                continue;
            }

            // 4. AUTO-CREATE KELAS: Cari Kelas. Jika tidak ketemu, otomatis buatkan kelas baru!
            $classroom = Classroom::firstOrCreate(
                ['school_id' => $schoolId, 'name' => $className],
                ['level' => 'Umum'] // Jika otomatis dibuat, tingkatnya diset 'Umum'
            );

            // 5. Simpan Data Siswa dan Generate QR secara otomatis
            $this->studentRepo->create([
                'school_id' => $schoolId,
                'classroom_id' => $classroom->id,
                'nis' => $nis,
                'name' => $name,
                'gender' => $gender,
                'parent_phone' => $phone,
                'qr_code_string' => 'SCANATTEND-' . Str::uuid()->toString()
            ]);

            $successCount++;
        }
        
        // Tutup file setelah selesai
        fclose($handle);

        // Kembalikan laporan hasil import
        return [
            'success_count' => $successCount,
            'errors' => $errors
        ];
    }
}