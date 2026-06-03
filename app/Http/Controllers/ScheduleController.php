<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScheduleService;
use App\Http\Requests\StoreScheduleRequest;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Exception;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            // Pilih sekolah dari URL, atau default ke sekolah pertama di database
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
            $schedules = $this->scheduleService->getSchedulesByCurrentSchool($selectedSchoolId);
        } else {
            $schedules = $this->scheduleService->getSchedulesByCurrentSchool();
        }

        return view('admin.schedules.index', compact('schedules', 'schools', 'selectedSchoolId'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        if ($user->hasRole('Super Admin')) {
            $schoolId = $request->query('school_id');
            if (!$schoolId) {
                return redirect()->route('admin.schedules.index')->with('error', 'Pilih sekolah dari Dropdown terlebih dahulu sebelum menambah jadwal.');
            }
        }

        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('name')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        $teachers = User::role(['Guru Mata Pelajaran', 'Wali Kelas'])
            ->where('school_id', $schoolId)
            ->orderBy('name')->get();

        return view('admin.schedules.create', compact('classrooms', 'subjects', 'teachers', 'schoolId'));
    }

    public function store(StoreScheduleRequest $request)
    {
        try {
            // Ambil school_id tersembunyi dari form untuk Super Admin
            $schoolId = $request->input('school_id'); 
            $this->scheduleService->createSchedule($request->validated(), $schoolId);
            
            // Arahkan kembali ke URL dengan parameter school_id agar filter tetap aktif
            return redirect()->route('admin.schedules.index', ['school_id' => $schoolId])
                ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->scheduleService->deleteSchedule($id);
        return back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}