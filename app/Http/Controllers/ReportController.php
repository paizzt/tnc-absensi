<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
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
        }

        $classrooms = Classroom::where('school_id', $selectedSchoolId)->orderBy('name')->get();

        return view('admin.reports.index', compact('schools', 'selectedSchoolId', 'classrooms'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,csv'
        ]);

        $schoolId = $request->input('school_id');
        $classroomId = $request->input('classroom_id');

        $query = Attendance::with(['student.classroom'])
            ->where('school_id', $schoolId)
            ->whereBetween('date', [$request->start_date, $request->end_date]);

        if ($classroomId) {
            $query->whereHas('student', function($q) use ($classroomId) {
                $q->where('classroom_id', $classroomId);
            });
        }

        $attendances = $query->orderBy('date', 'asc')->get();
        $school = School::find($schoolId);

        if ($request->format == 'csv') {
            return $this->exportCsv($attendances, $school);
        } else {
            return view('admin.reports.print', compact('attendances', 'request', 'school'));
        }
    }

    private function exportCsv($attendances, $school)
    {
        $fileName = 'Rekap_Absensi_' . str_replace(' ', '_', $school->name) . '_' . date('Ymd') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Status', 'Jam Masuk', 'Jam Pulang']);

            foreach ($attendances as $row) {
                fputcsv($file, [
                    $row->date,
                    $row->student->nis ?? '-',
                    $row->student->name ?? '-',
                    $row->student->classroom->name ?? '-',
                    $row->status,
                    $row->scan_in ?? '-',
                    $row->scan_out ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}