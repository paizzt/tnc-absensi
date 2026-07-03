<?php

namespace App\Services;

use App\Repositories\Contracts\CounselingRepositoryInterface;
use App\Models\Student;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class CounselingService
{
    protected $repo;

    public function __construct(CounselingRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getDashboardData($requestedSchoolId = null)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            if (!$requestedSchoolId) {
                // Return collection kosong jika belum pilih sekolah
                return collect([]);
            }
            return $this->repo->getStudentsWithBadAttendance($requestedSchoolId);
        }

        $schoolId = $user->school_id;
        if (!$schoolId) {
            abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
        }

        return $this->repo->getStudentsWithBadAttendance($schoolId);
    }

    public function sendWarningLetter(array $data, $file)
    {
        DB::beginTransaction();
        try {
            $student = Student::findOrFail($data['student_id']);
            $user = Auth::user();
            
            // Validasi kepemilikan sekolah (Kecuali Super Admin)
            if (!$user->hasRole('Super Admin') && $student->school_id !== $user->school_id) {
                throw new Exception("Akses ditolak. Anda tidak bisa mengirim SP ke siswa sekolah lain.");
            }

            $path = $file->store('warning_letters', 'public');
            
            $this->repo->createWarningLetter([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'sp_level' => $data['sp_level'],
                'document_path' => $path,
                'notes' => $data['notes'] ?? 'Pemanggilan orang tua terkait tingkat absensi yang buruk.'
            ]);

            $documentUrl = asset('storage/' . $path);

            $msg = "*SURAT PANGGILAN (SP-{$data['sp_level']})*\n\nYth. Orang Tua/Wali dari Ananda *{$student->name}*,\n\nKami menginformasikan bahwa tingkat kehadiran ananda saat ini berada di bawah batas standar sekolah. Oleh karena itu, kami mengundang Bapak/Ibu untuk hadir ke ruang BK.\n\nUnduh/Lihat Surat Panggilan resmi pada tautan berikut:\n{$documentUrl}\n\nHarap segera menindaklanjuti pesan ini.\n_Bimbingan Konseling - SCANATTEND_";
            
            SendWhatsAppNotification::dispatch($student->parent_phone, $msg, $student->school_id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}