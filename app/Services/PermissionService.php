<?php

namespace App\Services;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\SendWhatsAppNotification;
use Carbon\Carbon;
use Exception;

class PermissionService
{
    protected $repo;
    protected $attendanceRepo;

    public function __construct(PermissionRepositoryInterface $repo, AttendanceRepositoryInterface $attendanceRepo)
    {
        $this->repo = $repo;
        $this->attendanceRepo = $attendanceRepo;
    }

    public function submitRequest($student, array $data, $documentFile = null, $selfieBase64 = null)
    {
        $documentPath = null;
        $selfiePath = null;

        if ($documentFile) {
            $documentPath = $documentFile->store('permissions/documents', 'public');
        }

        if ($selfieBase64) {
            $image_parts = explode(";base64,", $selfieBase64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            $fileName = 'selfie_' . Str::uuid() . '.' . $image_type;
            Storage::disk('public')->put('permissions/selfies/' . $fileName, $image_base64);
            $selfiePath = 'permissions/selfies/' . $fileName;
        }

        return $this->repo->create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'type' => $data['type'],
            'reason' => $data['reason'],
            'document_path' => $documentPath,
            'selfie_path' => $selfiePath,
            'status' => 'Menunggu'
        ]);
    }

    public function getRequestsForCurrentSchool()
    {
        return $this->repo->getPaginatedBySchool(Auth::user()->school_id, 15);
    }

    public function getRequestById(string $id)
    {
        return $this->repo->findById($id);
    }

    public function approveRequest(string $id)
    {
        DB::beginTransaction();
        try {
            $req = $this->repo->findById($id);
            if ($req->status !== 'Menunggu') throw new Exception('Permohonan ini sudah diproses sebelumnya.');

            // 1. Ubah status izin menjadi Disetujui
            $this->repo->update($id, ['status' => 'Disetujui']);

            // 2. Catat ke tabel absensi harian (Gate Attendance)
            $attendance = $this->attendanceRepo->findByStudentAndDate($req->student_id, $req->date);
            if ($attendance) {
                $this->attendanceRepo->update($attendance->id, ['status' => $req->type]);
            } else {
                $this->attendanceRepo->create([
                    'school_id' => $req->school_id,
                    'student_id' => $req->student_id,
                    'date' => $req->date,
                    'status' => $req->type
                ]);
            }

            // 3. Kirim Notifikasi WA
            $msg = "*INFORMASI VALIDASI IZIN*\nYth. Orang Tua/Wali,\nPermohonan {$req->type} untuk Ananda *{$req->student->name}* pada tanggal " . date('d/m/Y', strtotime($req->date)) . " telah *DISETUJUI* oleh Wali Kelas.\n\n_Pesan otomatis oleh SCANATTEND_";
            SendWhatsAppNotification::dispatch($req->student->parent_phone, $msg);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rejectRequest(string $id)
    {
        $req = $this->repo->findById($id);
        if ($req->status !== 'Menunggu') throw new Exception('Permohonan ini sudah diproses sebelumnya.');

        $this->repo->update($id, ['status' => 'Ditolak']);

        $msg = "*INFORMASI VALIDASI IZIN*\nYth. Orang Tua/Wali,\nMohon maaf, permohonan {$req->type} untuk Ananda *{$req->student->name}* *DITOLAK* dikarenakan tidak memenuhi syarat verifikasi keamanan. Harap segera menghubungi Wali Kelas/Guru BK.\n\n_Pesan otomatis oleh SCANATTEND_";
        SendWhatsAppNotification::dispatch($req->student->parent_phone, $msg);
    }
}