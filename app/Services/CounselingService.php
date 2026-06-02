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

    public function getDashboardData()
    {
        return $this->repo->getStudentsWithBadAttendance(Auth::user()->school_id);
    }

    public function sendWarningLetter(array $data, $file)
    {
        DB::beginTransaction();
        try {
            $student = Student::findOrFail($data['student_id']);
            
            if ($student->school_id !== Auth::user()->school_id) {
                throw new Exception("Akses ditolak.");
            }

            // Upload Surat Panggilan
            $path = $file->store('warning_letters', 'public');
            
            // Simpan Data SP
            $this->repo->createWarningLetter([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'sp_level' => $data['sp_level'],
                'document_path' => $path,
                'notes' => $data['notes'] ?? 'Pemanggilan orang tua terkait tingkat absensi yang buruk.'
            ]);

            // Dapatkan URL absolut untuk lampiran PDF/Gambar agar bisa diklik di WA
            $documentUrl = asset('storage/' . $path);

            // Kirim WA
            $msg = "*SURAT PANGGILAN (SP-{$data['sp_level']})*\n\nYth. Orang Tua/Wali dari Ananda *{$student->name}*,\n\nKami menginformasikan bahwa tingkat kehadiran ananda saat ini berada di bawah batas standar sekolah. Oleh karena itu, kami mengundang Bapak/Ibu untuk hadir ke ruang BK.\n\nUnduh/Lihat Surat Panggilan resmi pada tautan berikut:\n{$documentUrl}\n\nHarap segera menindaklanjuti pesan ini.\n_Bimbingan Konseling - SCANATTEND_";
            
            SendWhatsAppNotification::dispatch($student->parent_phone, $msg);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}