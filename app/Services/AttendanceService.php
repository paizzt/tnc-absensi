<?php

namespace App\Services;

use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Models\Student;
use App\Models\SchoolSetting;
use App\Jobs\SendWhatsAppNotification; // Import Job
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class AttendanceService
{
    protected $attendanceRepo;

    public function __construct(AttendanceRepositoryInterface $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    public function processScan(string $qrCode)
    {
        $schoolId = Auth::user()->school_id;
        
        $student = Student::with('classroom')->where('qr_code_string', $qrCode)->where('school_id', $schoolId)->first();
        if (!$student) {
            throw new Exception('QR Code tidak dikenali atau siswa tidak terdaftar.');
        }
        if (!$student->is_active) {
            throw new Exception('Status siswa saat ini tidak aktif.');
        }

        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');
        $settings = SchoolSetting::where('school_id', $schoolId)->first();

        $attendance = $this->attendanceRepo->findByStudentAndDate($student->id, $date);
        $isAfternoon = $time >= '12:00:00'; 

        if (!$attendance) {
            if ($isAfternoon) {
                // Absen Pulang (Tanpa Masuk)
                $this->attendanceRepo->create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'date' => $date,
                    'scan_out' => $time,
                    'status' => 'Bolos' 
                ]);
                
                // Trigger Queue WhatsApp
                if ($settings->notify_out) {
                    $msg = "*NOTIFIKASI KEPULANGAN*\nYth. Orang Tua/Wali,\nAnanda *{$student->name}* telah melakukan absensi pulang dari sekolah pada pukul *{$time} WITA*.\n\n_Pesan otomatis oleh SCANATTEND_";
                    SendWhatsAppNotification::dispatch($student->parent_phone, $msg);
                }

                return ['student' => $student, 'message' => 'Absen Pulang berhasil dicatat.', 'type' => 'out'];
            } else {
                // Absen Masuk Normal
                $status = ($time > $settings->time_late) ? 'Terlambat' : 'Hadir';
                $this->attendanceRepo->create([
                    'school_id' => $schoolId,
                    'student_id' => $student->id,
                    'date' => $date,
                    'scan_in' => $time,
                    'status' => $status
                ]);
                
                // Trigger Queue WhatsApp
                if ($settings->notify_in) {
                    $msg = "*NOTIFIKASI KEHADIRAN*\nYth. Orang Tua/Wali,\nAnanda *{$student->name}* telah hadir di sekolah pada pukul *{$time} WITA* dengan status: *{$status}*.\n\n_Pesan otomatis oleh SCANATTEND_";
                    SendWhatsAppNotification::dispatch($student->parent_phone, $msg);
                }

                return ['student' => $student, 'message' => 'Absen Masuk berhasil: ' . $status, 'type' => 'in'];
            }
        } else {
            if ($attendance->scan_out) {
                throw new Exception('Siswa ini sudah melakukan absen pulang sebelumnya.');
            }
            
            // Catat Absen Pulang
            $this->attendanceRepo->update($attendance->id, [
                'scan_out' => $time
            ]);
            
            // Trigger Queue WhatsApp
            if ($settings->notify_out) {
                $msg = "*NOTIFIKASI KEPULANGAN*\nYth. Orang Tua/Wali,\nAnanda *{$student->name}* telah melakukan absensi pulang dari sekolah pada pukul *{$time} WITA*.\n\n_Pesan otomatis oleh SCANATTEND_";
                SendWhatsAppNotification::dispatch($student->parent_phone, $msg);
            }

            return ['student' => $student, 'message' => 'Absen Pulang berhasil dicatat.', 'type' => 'out'];
        }
    }
}