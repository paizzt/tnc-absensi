<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClassroomService;
use App\Http\Requests\SaveClassroomRequest;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    protected $classroomService;

    public function __construct(ClassroomService $classroomService)
    {
        $this->classroomService = $classroomService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
            $classrooms = $this->classroomService->getClassroomsByCurrentSchool($selectedSchoolId);
        } else {
            $classrooms = $this->classroomService->getClassroomsByCurrentSchool();
            $selectedSchoolId = $user->school_id; // Agar form tetap ingat ID jika bukan Super Admin
        }

        return view('admin.classrooms.index', compact('classrooms', 'schools', 'selectedSchoolId'));
    }

    public function store(SaveClassroomRequest $request)
    {
        try {
            $schoolId = $request->input('school_id');
            $this->classroomService->createClassroom($request->validated(), $schoolId);
            
            return redirect()->route('admin.classrooms.index', ['school_id' => $schoolId])
                ->with('success', 'Data kelas berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(SaveClassroomRequest $request, $id)
    {
        $schoolId = $request->input('school_id');
        $this->classroomService->updateClassroom($id, $request->validated());
        
        return redirect()->route('admin.classrooms.index', ['school_id' => $schoolId])
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->classroomService->deleteClassroom($id);
        return back()->with('success', 'Data kelas berhasil dihapus.');
    }
}