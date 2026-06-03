<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubjectService;
use App\Http\Requests\SaveSubjectRequest;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
            $subjects = $this->subjectService->getSubjectsByCurrentSchool($selectedSchoolId);
        } else {
            $subjects = $this->subjectService->getSubjectsByCurrentSchool();
            $selectedSchoolId = $user->school_id; // Agar form tetap ingat ID jika bukan Super Admin
        }

        return view('admin.subjects.index', compact('subjects', 'schools', 'selectedSchoolId'));
    }

    public function store(SaveSubjectRequest $request)
    {
        try {
            $schoolId = $request->input('school_id');
            $this->subjectService->createSubject($request->validated(), $schoolId);
            
            return redirect()->route('admin.subjects.index', ['school_id' => $schoolId])
                ->with('success', 'Mata Pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(SaveSubjectRequest $request, $id)
    {
        $schoolId = $request->input('school_id');
        $this->subjectService->updateSubject($id, $request->validated());
        
        return redirect()->route('admin.subjects.index', ['school_id' => $schoolId])
            ->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->subjectService->deleteSubject($id);
        return back()->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}