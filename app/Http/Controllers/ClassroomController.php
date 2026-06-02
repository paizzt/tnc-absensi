<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClassroomService;
use App\Http\Requests\SaveClassroomRequest;

class ClassroomController extends Controller
{
    protected $classroomService;

    public function __construct(ClassroomService $classroomService)
    {
        $this->classroomService = $classroomService;
    }

    public function index()
    {
        $classrooms = $this->classroomService->getClassroomsByCurrentSchool();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function store(SaveClassroomRequest $request)
    {
        $this->classroomService->createClassroom($request->validated());
        return redirect()->route('admin.classrooms.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(SaveClassroomRequest $request, $id)
    {
        $this->classroomService->updateClassroom($id, $request->validated());
        return redirect()->route('admin.classrooms.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->classroomService->deleteClassroom($id);
        return redirect()->route('admin.classrooms.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}