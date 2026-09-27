<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\GateAttendance;
use App\Models\Classroom;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticController extends Controller
{
    public function index()
    {
        // Data 1: Jumlah Siswa per Kelas
        $studentsPerClass = Classroom::withCount('students')->get();
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
            $onTime = GateAttendance::where('date', $dateStr)
                ->where('status', 'Hadir')
                ->count();
            $late = GateAttendance::where('date', $dateStr)
                ->where('status', 'Terlambat')
                ->count();

            $onTimeData[] = $onTime;
            $lateData[] = $late;
        }

        // Data 3: Status Kehadiran Hari Ini
        $today = Carbon::today()->format('Y-m-d');
        $todayAttendances = GateAttendance::where('date', $today)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
            
        $statusLabels = $todayAttendances->keys();
        $statusCounts = $todayAttendances->values();

        return view('admin.analytics.index', compact(
            'classLabels', 'studentCounts',
            'last7Days', 'onTimeData', 'lateData',
            'statusLabels', 'statusCounts'
        ));
    }
}
