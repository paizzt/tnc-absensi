<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScheduleService;
use App\Http\Requests\StoreScheduleRequest;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index()
    {
        $schedules = $this->scheduleService->getSchedulesByCurrentSchool();
        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('name')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        
        // Ambil User yang memiliki Role "Guru Mata Pelajaran" atau "Wali Kelas" di sekolah ini
        $teachers = User::role(['Guru Mata Pelajaran', 'Wali Kelas'])
            ->where('school_id', $schoolId)
            ->orderBy('name')->get();

        return view('admin.schedules.create', compact('classrooms', 'subjects', 'teachers'));
    }

    public function store(StoreScheduleRequest $request)
    {
        try {
            $this->scheduleService->createSchedule($request->validated());
            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->scheduleService->deleteSchedule($id);
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}