<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ScheduleService;
use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use App\Models\School;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    // --- FUNGSI INDEX & CREATE (Sama seperti sebelumnya) ---
    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
        } else {
            $selectedSchoolId = $user->school_id;
        }

        $setting = SchoolSetting::where('school_id', $selectedSchoolId)->first();
        $timeIn = $setting ? $setting->time_in : '07:00:00';
        $lessonDuration = $setting->lesson_duration ?? 45;
        $breakDuration = $setting->break_duration ?? 30;
        $breakAfter = $setting->break_after_lesson ?? 4;

        $timeSlots = [];
        $currentTime = Carbon::parse($timeIn);
        $totalLessons = 9;

        for ($i = 1; $i <= $totalLessons; $i++) {
            $start = $currentTime->format('H:i');
            $currentTime->addMinutes($lessonDuration);
            $end = $currentTime->format('H:i');

            $timeSlots[] = [
                'name' => 'Jam ' . $i, 'time' => $start . '-' . $end,
                'start' => $start, 'end' => $end, 'is_break' => false
            ];

            if ($i == $breakAfter) {
                $bStart = $currentTime->format('H:i');
                $currentTime->addMinutes($breakDuration);
                $bEnd = $currentTime->format('H:i');
                
                $timeSlots[] = [
                    'name' => 'Istirahat', 'time' => $bStart . '-' . $bEnd,
                    'start' => $bStart, 'end' => $bEnd, 'is_break' => true
                ];
            }
        }

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $schedules = Schedule::with(['classroom', 'subject', 'teacher'])
            ->where('school_id', $selectedSchoolId)->get()->groupBy('classroom_id');

        $classrooms = Classroom::where('school_id', $selectedSchoolId)
            ->whereIn('id', $schedules->keys())->orderBy('level')->orderBy('name')->get();

        return view('admin.schedules.index', compact('classrooms', 'schedules', 'schools', 'selectedSchoolId', 'timeSlots', 'days'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->hasRole('Super Admin') ? ($request->query('school_id') ?? School::first()->id) : $user->school_id;

        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('name')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        $teachers = User::role(['Guru Mata Pelajaran', 'Wali Kelas'])->where('school_id', $schoolId)->orderBy('name')->get();

        $setting = SchoolSetting::where('school_id', $schoolId)->first();
        $timeIn = $setting ? $setting->time_in : '07:00:00';
        $currentTime = Carbon::parse($timeIn);
        $timeSlots = [];
        for ($i = 1; $i <= 9; $i++) {
            $start = $currentTime->format('H:i');
            $currentTime->addMinutes($setting->lesson_duration ?? 45);
            $end = $currentTime->format('H:i');
            $timeSlots[] = ['name' => 'Jam ' . $i, 'time' => "$start-$end", 'start' => $start, 'end' => $end, 'is_break' => false];
            
            if ($i == ($setting->break_after_lesson ?? 4)) {
                $bStart = $currentTime->format('H:i');
                $currentTime->addMinutes($setting->break_duration ?? 30);
                $bEnd = $currentTime->format('H:i');
                $timeSlots[] = ['name' => 'Istirahat', 'time' => "$bStart-$bEnd", 'start' => $bStart, 'end' => $bEnd, 'is_break' => true];
            }
        }

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return view('admin.schedules.create', compact('classrooms', 'subjects', 'teachers', 'schoolId', 'timeSlots', 'days'));
    }

    public function store(Request $request)
    {
        $request->validate(['classroom_id' => 'required|exists:classrooms,id', 'roster' => 'array']);
        try {
            $this->scheduleService->createBulkSchedule($request->all(), $request->input('school_id'));
            return redirect()->route('admin.schedules.index', ['school_id' => $request->input('school_id')])->with('success', 'Roster Kelas berhasil disimpan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->scheduleService->deleteSchedule($id);
        return back()->with('success', 'Mapel berhasil dihapus dari jadwal.');
    }

    // =========================================================================
    // FUNGSI BARU: EDIT & DELETE PER KELAS
    // =========================================================================

    public function edit($id)
    {
        $classroom = Classroom::findOrFail($id);
        $schoolId = $classroom->school_id;
        
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        $teachers = User::role(['Guru Mata Pelajaran', 'Wali Kelas'])->where('school_id', $schoolId)->orderBy('name')->get();
        $existingSchedules = Schedule::where('classroom_id', $id)->get();

        // Regenerate Time Slots
        $setting = SchoolSetting::where('school_id', $schoolId)->first();
        $currentTime = Carbon::parse($setting ? $setting->time_in : '07:00:00');
        $timeSlots = [];
        for ($i = 1; $i <= 9; $i++) {
            $start = $currentTime->format('H:i');
            $currentTime->addMinutes($setting->lesson_duration ?? 45);
            $end = $currentTime->format('H:i');
            $timeSlots[] = ['name' => 'Jam '.$i, 'time' => "$start-$end", 'start' => $start, 'end' => $end, 'is_break' => false];
            if ($i == ($setting->break_after_lesson ?? 4)) {
                $bStart = $currentTime->format('H:i');
                $currentTime->addMinutes($setting->break_duration ?? 30);
                $bEnd = $currentTime->format('H:i');
                $timeSlots[] = ['name' => 'Istirahat', 'time' => "$bStart-$bEnd", 'start' => $bStart, 'end' => $bEnd, 'is_break' => true];
            }
        }
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('admin.schedules.edit', compact('classroom', 'subjects', 'teachers', 'timeSlots', 'days', 'existingSchedules', 'schoolId'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['roster' => 'array']);
        $schoolId = Classroom::findOrFail($id)->school_id;

        DB::beginTransaction();
        try {
            // Hapus jadwal lama milik kelas ini
            Schedule::where('classroom_id', $id)->delete();

            // Masukkan jadwal baru (Upsert / Update-Insert)
            $data = $request->all();
            $data['classroom_id'] = $id; // Set ID kelas secara paksa
            $this->scheduleService->createBulkSchedule($data, $schoolId);

            DB::commit();
            return redirect()->route('admin.schedules.index', ['school_id' => $schoolId])->with('success', 'Perubahan Roster berhasil disimpan.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroyClass($id)
    {
        Schedule::where('classroom_id', $id)->delete();
        return back()->with('success', 'Seluruh Jadwal pada kelas tersebut berhasil direset (dihapus).');
    }
}