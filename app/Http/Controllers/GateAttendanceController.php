<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\GateAttendance;
use App\Models\StudentExit;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GateAttendanceController extends Controller
{
    public function index()
    {
        return view('admin.attendances.gate');
    }

    public function scan(Request $request)
    {
        $request->validate(['qr_code' => 'required']);
        $student = Student::with('classroom')->where('qr_code_string', $request->qr_code)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak dikenali dalam sistem.']);
        }

        $now = Carbon::now();
        $today = $now->toDateString();
        $schoolSetting = SchoolSetting::where('school_id', $student->school_id)->first();
        
        // Default pengaturan waktu jika belum diatur
        $limitTimeIn = $schoolSetting ? Carbon::parse($schoolSetting->time_late) : Carbon::parse('07:15:00');
        $timeOutStart = $schoolSetting ? Carbon::parse($schoolSetting->time_out) : Carbon::parse('15:00:00');

        // ==========================================
        // 1. PENGECEKAN IZIN KELUAR SEMENTARA
        // ==========================================
        $activeExit = StudentExit::where('student_id', $student->id)
            ->whereIn('status', ['Disetujui', 'Keluar'])
            ->whereDate('created_at', $today)
            ->first();

        if ($activeExit) {
            if ($activeExit->status == 'Disetujui') {
                $activeExit->update([
                    'status' => 'Keluar', 
                    'scanned_out_at' => $now
                ]);
                
                $pesan = "*PEMBERITAHUAN IZIN KELUAR*\n\nAnanda *{$student->name}* telah keluar area sekolah pada pukul *" . $now->format('H:i') . "*.\n\nAlasan: {$activeExit->reason}\nBatas Izin: " . $activeExit->valid_until->format('H:i') . "\n\nMohon pantau aktivitas ananda.";
                $this->sendWhatsApp($student->parent_phone, $pesan, $schoolSetting->fonnte_token ?? null);

                return response()->json([
                    'success' => true, 
                    'message' => "Izin Keluar Dikonfirmasi ({$activeExit->reason})", 
                    'student' => $student
                ]);

            } elseif ($activeExit->status == 'Keluar') {
                $statusAkhir = $now->greaterThan($activeExit->valid_until) ? 'Terlambat' : 'Kembali';
                
                $activeExit->update([
                    'status' => $statusAkhir, 
                    'scanned_in_at' => $now
                ]);
                
                $pesan = "*PEMBERITAHUAN KEMBALI*\n\nAnanda *{$student->name}* telah kembali memasuki area sekolah pada pukul *" . $now->format('H:i') . "*.\n\nTerima kasih.";
                $this->sendWhatsApp($student->parent_phone, $pesan, $schoolSetting->fonnte_token ?? null);

                return response()->json([
                    'success' => true, 
                    'message' => "Siswa Kembali ke Sekolah", 
                    'student' => $student
                ]);
            }
        }

        // ==========================================
        // 2. ABSENSI REGULER (MASUK & PULANG)
        // ==========================================
        $attendance = GateAttendance::where('student_id', $student->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Proses Absen Masuk
            $status = $now->greaterThan($limitTimeIn) ? 'Terlambat' : 'Hadir';
            
            GateAttendance::create([
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'date' => $today,
                'scan_in' => $now->format('H:i:s'),
                'status' => $status
            ]);

            // Kirim Notifikasi WA jika fitur aktif
            if ($schoolSetting && $schoolSetting->notify_in) {
                $pesan = "*LAPORAN ABSENSI MASUK*\n\nAnanda *{$student->name}* telah melakukan absen masuk pada pukul *" . $now->format('H:i') . "*.\nStatus: {$status}\n\nTerima kasih.";
                $this->sendWhatsApp($student->parent_phone, $pesan, $schoolSetting->fonnte_token ?? null);
            }

            return response()->json(['success' => true, 'message' => "Absen Masuk Berhasil ({$status})", 'student' => $student]);

        } else {
            // Proses Absen Pulang
            if ($attendance->scan_out) {
                return response()->json(['success' => false, 'message' => 'Siswa ini sudah melakukan absen pulang sebelumnya.']);
            }

            // Tolak jika belum jam pulang (Opsional, matikan baris di bawah ini jika boleh pulang cepat tanpa izin)
            // if ($now->lessThan($timeOutStart)) {
            //     return response()->json(['success' => false, 'message' => 'Belum waktunya jam pulang.']);
            // }

            $attendance->update([
                'scan_out' => $now->format('H:i:s')
            ]);

            // Kirim Notifikasi WA jika fitur aktif
            if ($schoolSetting && $schoolSetting->notify_out) {
                $pesan = "*LAPORAN ABSENSI PULANG*\n\nAnanda *{$student->name}* telah melakukan absen pulang pada pukul *" . $now->format('H:i') . "*.\n\nSemoga selamat sampai tujuan.";
                $this->sendWhatsApp($student->parent_phone, $pesan, $schoolSetting->fonnte_token ?? null);
            }

            return response()->json(['success' => true, 'message' => 'Absen Pulang Berhasil', 'student' => $student]);
        }
    }

    private function sendWhatsApp($phone, $message, $customToken = null)
    {
        // Pastikan Anda mengisi Token Fonnte yang aktif di pengaturan server Anda
        $token = $customToken ?: env('FONNTE_TOKEN', ''); 
        
        if (empty($token) || empty($phone)) {
            return false;
        }
        try {
            Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);
        } catch (\Exception $e) {
            // Gagal kirim diabaikan agar flow aplikasi tidak berhenti
        }
    }
}