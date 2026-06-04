<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Services\PermissionService;
use Exception;

class ParentPortalController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        return view('portal.izin.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string'
        ]);
        
        $identifier = $request->identifier;

        // Sistem mencari berdasarkan NIS atau Kode Rahasia QR Code
        $student = Student::with('classroom')
            ->where('nis', $identifier)
            ->orWhere('qr_code_string', $identifier)
            ->first();

        if (!$student) {
            return back()->with('error', 'Data siswa tidak ditemukan. Pastikan NIS atau QR Code valid.');
        }

        return redirect()->route('portal.izin.form', $student->id);
    }

    public function form($id)
    {
        $student = Student::with('classroom.school')->findOrFail($id);
        return view('portal.izin.form', compact('student'));
    }

    public function submit(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:Sakit,Izin',
            'reason' => 'required|string|max:255',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'selfie_image' => 'required|string'
        ]);

        try {
            $student = Student::findOrFail($id);
            $this->permissionService->submitRequest(
                $student, 
                $request->only(['type', 'reason']), 
                $request->file('document'), 
                $request->input('selfie_image')
            );

            return redirect()->route('portal.izin.index')->with('success', 'Permohonan berhasil dikirim ke Wali Kelas. Silakan tunggu persetujuan.');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}