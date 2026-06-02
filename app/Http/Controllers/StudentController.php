<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentService;
use App\Services\ClassroomService;
use App\Http\Requests\StoreStudentRequest;

class StudentController extends Controller
{
    protected $studentService;
    protected $classroomService;

    public function __construct(StudentService $studentService, ClassroomService $classroomService)
    {
        $this->studentService = $studentService;
        $this->classroomService = $classroomService;
    }

    public function index()
    {
        $students = $this->studentService->getStudentsByCurrentSchool();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        // Tarik data kelas untuk form dropdown
        $classrooms = \App\Models\Classroom::where('school_id', \Illuminate\Support\Facades\Auth::user()->school_id)
            ->orderBy('level')->orderBy('name')->get();
            
        return view('admin.students.create', compact('classrooms'));
    }

    public function store(StoreStudentRequest $request)
    {
        $this->studentService->createStudent($request->validated());
        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil ditambahkan dan QR Code otomatis dibuat.');
    }
}