<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Services\PermissionService;

class ParentPortalController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    // Halaman pencarian NIS
    public function index()
    {
        return view('portal.izin.index');
    }

    // Memproses pencarian NIS
    public function search(Request $request)
    {
        $request->validate(['nis' => 'required|string']);

        $student = Student::with(['school', 'classroom'])->where('nis', $request->nis)->first();

        if (!$student) {
            return back()->with('error', 'Data siswa tidak ditemukan. Pastikan NIS benar.');
        }

        // Redirect ke form pengisian dengan membawa ID Siswa
        return redirect()->route('portal.izin.form', ['student_id' => $student->id]);
    }

    // Menampilkan form izin beserta kamera
    public function form($student_id)
    {
        $student = Student::with(['school', 'classroom'])->findOrFail($student_id);
        return view('portal.izin.form', compact('student'));
    }

    // Memproses pengiriman data izin
    public function submit(Request $request, $student_id)
    {
        $student = Student::findOrFail($student_id);

        $request->validate([
            'type' => 'required|in:Sakit,Izin',
            'reason' => 'required|string',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'selfie_image' => 'required|string' // Ini akan berisi data Base64 dari kamera
        ]);

        if ($request->type === 'Sakit' && !$request->hasFile('document')) {
            return back()->with('error', 'Surat dokter atau bukti foto wajib dilampirkan untuk pengajuan Sakit.');
        }

        $this->permissionService->submitRequest(
            $student, 
            $request->only(['type', 'reason']), 
            $request->file('document'), 
            $request->selfie_image
        );

        return redirect()->route('portal.izin.index')->with('success', 'Pengajuan izin berhasil dikirim. Wali kelas akan segera melakukan validasi.');
    }
}