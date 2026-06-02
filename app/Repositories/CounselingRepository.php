<?php

namespace App\Repositories;

use App\Models\Student;
use App\Models\GateAttendance;
use App\Models\WarningLetter;
use App\Repositories\Contracts\CounselingRepositoryInterface;

class CounselingRepository implements CounselingRepositoryInterface
{
    public function getStudentsWithBadAttendance(string $schoolId)
    {
        $students = Student::with(['classroom', 'school'])->where('school_id', $schoolId)->get();
        $badStudents = collect();

        foreach ($students as $student) {
            $totalDays = GateAttendance::where('student_id', $student->id)->count();
            
            if ($totalDays > 0) {
                $presentDays = GateAttendance::where('student_id', $student->id)
                    ->whereIn('status', ['Hadir', 'Terlambat'])->count();
                
                $percentage = round(($presentDays / $totalDays) * 100);
                
                // Cek jika persentase kurang dari 80%
                if ($percentage < 80) {
                    $student->attendance_percentage = $percentage;
                    $student->total_alpha = GateAttendance::where('student_id', $student->id)
                        ->whereIn('status', ['Alpha', 'Bolos'])->count();
                    
                    // Ambil histori SP terakhir
                    $lastSp = WarningLetter::where('student_id', $student->id)->orderBy('sp_level', 'desc')->first();
                    $student->last_sp = $lastSp ? $lastSp->sp_level : 0;
                    
                    $badStudents->push($student);
                }
            }
        }

        return $badStudents->sortBy('attendance_percentage');
    }

    public function createWarningLetter(array $data)
    {
        return WarningLetter::create($data);
    }
}