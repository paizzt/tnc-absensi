<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttendanceService;
use Exception;

class GateAttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    // Menampilkan halaman scanner
    public function index()
    {
        return view('admin.attendances.gate');
    }

    // Memproses permintaan AJAX dari scanner
    public function scan(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        try {
            $result = $this->attendanceService->processScan($request->qr_code);
            
            // Nanti di sini kita akan trigger Queue untuk kirim WhatsApp
            
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'student_name' => $result['student']->name,
                'student_nis' => $result['student']->nis,
                'classroom' => $result['student']->classroom->name,
                'type' => $result['type']
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}