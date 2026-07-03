<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SchoolService;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    protected $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');

        if ($user->hasRole('Super Admin')) {
            // Super Admin melihat semua sekolah dengan fitur pencarian teks
            $schools = School::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('npsn', 'like', "%{$search}%");
            })->orderBy('name')->paginate(15);
        } else {
            // Admin Sekolah hanya melihat profil sekolahnya sendiri (1 baris)
            $schools = School::where('id', $user->school_id)->paginate(1);
        }

        return view('superadmin.schools.index', compact('schools', 'search'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Akses Ditolak. Hanya Super Admin yang dapat mendaftarkan sekolah baru.');
        }
        return view('superadmin.schools.create');
    }

    public function store(StoreSchoolRequest $request)
    {
        if (!Auth::user()->hasRole('Super Admin')) abort(403);
        
        $this->schoolService->createSchoolWithDefaultSettings($request->validated());
        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil didaftarkan.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        // Proteksi: Admin Sekolah tidak boleh mengedit sekolah lain
        if (!$user->hasRole('Super Admin') && $user->school_id !== $id) {
            abort(403, 'Akses Ditolak. Anda tidak berhak mengedit data sekolah lain.');
        }

        $school = $this->schoolService->getSchoolById($id);
        return view('superadmin.schools.edit', compact('school'));
    }

    public function update(UpdateSchoolRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('Super Admin') && $user->school_id !== $id) {
            abort(403, 'Akses Ditolak. Anda tidak berhak mengubah data sekolah lain.');
        }

        $this->schoolService->updateSchool($id, $request->validated());
        return redirect()->route('schools.index')->with('success', 'Profil sekolah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Super Admin')) {
            abort(403, 'Akses Ditolak. Hanya Super Admin yang dapat menghapus sekolah.');
        }

        $this->schoolService->deleteSchool($id);
        return redirect()->route('schools.index')->with('success', 'Data sekolah berhasil dihapus.');
    }
}