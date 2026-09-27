<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\GateAttendance;
use App\Models\Classroom;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticController extends Controller
{
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
            if (!$selectedSchoolId) abort(403, 'Akun Anda belum ditugaskan ke sekolah manapun.');
        }

        // Data 1: Jumlah Siswa per Kelas
        $studentsPerClassQuery = Classroom::withCount(['students' => function ($query) {
            $query->whereNull('deleted_at'); // Asumsikan siswa tidak terhapus
        }]);

        if ($selectedSchoolId) {
            $studentsPerClassQuery->where('school_id', $selectedSchoolId);
        }

        $studentsPerClass = $studentsPerClassQuery->get();
        $classLabels = $studentsPerClass->pluck('name');
        $studentCounts = $studentsPerClass->pluck('students_count');

        // Data 2: Kehadiran Gerbang 7 Hari Terakhir
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }

        $onTimeData = [];
        $lateData = [];

        foreach ($last7Days as $dateStr) {
            $onTimeQuery = GateAttendance::where('date', $dateStr)->where('status', 'Hadir');
            $lateQuery = GateAttendance::where('date', $dateStr)->where('status', 'Terlambat');

            if ($selectedSchoolId) {
                $onTimeQuery->where('school_id', $selectedSchoolId);
                $lateQuery->where('school_id', $selectedSchoolId);
            }

            $onTimeData[] = $onTimeQuery->count();
            $lateData[] = $lateQuery->count();
        }

        // Data 3: Status Kehadiran Hari Ini
        $today = Carbon::today()->format('Y-m-d');
        $todayAttendancesQuery = GateAttendance::where('date', $today)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status');

        if ($selectedSchoolId) {
            $todayAttendancesQuery->where('school_id', $selectedSchoolId);
        }
            
        $todayAttendances = $todayAttendancesQuery->pluck('count', 'status');
            
        $statusLabels = $todayAttendances->keys();
        $statusCounts = $todayAttendances->values();

        return view('admin.analytics.index', compact(
            'classLabels', 'studentCounts',
            'last7Days', 'onTimeData', 'lateData',
            'statusLabels', 'statusCounts',
            'schools', 'selectedSchoolId'
        ));
    }
}
