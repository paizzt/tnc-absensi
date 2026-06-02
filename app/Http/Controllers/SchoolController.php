<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SchoolService;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest; // Tambahkan ini

class SchoolController extends Controller
{
    protected $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    public function index()
    {
        $schools = $this->schoolService->getAllSchools();
        return view('superadmin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('superadmin.schools.create');
    }

    public function store(StoreSchoolRequest $request)
    {
        $this->schoolService->createSchoolWithDefaultSettings($request->validated());
        return redirect()->route('schools.index')->with('success', 'Sekolah berhasil didaftarkan.');
    }

    // Tambahkan 3 fungsi di bawah ini:
    public function edit($id)
    {
        $school = $this->schoolService->getSchoolById($id);
        return view('superadmin.schools.edit', compact('school'));
    }

    public function update(UpdateSchoolRequest $request, $id)
    {
        $this->schoolService->updateSchool($id, $request->validated());
        return redirect()->route('schools.index')->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->schoolService->deleteSchool($id);
        return redirect()->route('schools.index')->with('success', 'Data sekolah berhasil dihapus (Soft Delete).');
    }
}