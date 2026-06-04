<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StudentService;
use App\Http\Requests\StoreStudentRequest;
use App\Models\School;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
            $students = $this->studentService->getStudentsByCurrentSchool($selectedSchoolId);
        } else {
            $students = $this->studentService->getStudentsByCurrentSchool();
            $selectedSchoolId = $user->school_id;
        }

        return view('admin.students.index', compact('students', 'schools', 'selectedSchoolId'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $schoolId = $user->school_id;
        $schools = [];

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $schoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
            if (!$schoolId) return redirect()->route('admin.students.index')->with('error', 'Pilih sekolah terlebih dahulu.');
        }

        $classrooms = Classroom::where('school_id', $schoolId)->orderBy('level')->orderBy('name')->get();
        return view('admin.students.create', compact('classrooms', 'schoolId', 'schools'));
    }

    public function store(StoreStudentRequest $request)
    {
        $schoolId = $request->input('school_id');
        $this->studentService->createStudent($request->validated(), $schoolId);
        return redirect()->route('admin.students.index', ['school_id' => $schoolId])->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=Template_Import_Siswa.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['NIS', 'Nama Lengkap', 'Nama Kelas', 'Jenis Kelamin (L/P)', 'No WA Orang Tua'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['10123', 'Ahmad Budi Santoso', 'X MIPA 1', 'L', '081234567890']);
            fputcsv($file, ['10124', 'Siti Aminah', 'X MIPA 2', 'P', '081987654321']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $user = Auth::user();
        $schoolId = $request->input('school_id') ?? $user->school_id;

        if ($user->hasRole('Super Admin') && !$schoolId) {
            return back()->with('error', 'Pilih sekolah dari dropdown terlebih dahulu.');
        }

        try {
            $result = $this->studentService->importStudentsCsv($request->file('csv_file'), $schoolId);
            
            $msg = "<b>" . $result['success_count'] . " data siswa berhasil diimpor dan QR Code dibuat.</b>";
            if (count($result['errors']) > 0) {
                $msg .= "<br>Namun ada beberapa error (dilewati): <ul class='mb-0 mt-1'>";
                $displayErrors = array_slice($result['errors'], 0, 5); 
                foreach ($displayErrors as $err) {
                    $msg .= "<li>$err</li>";
                }
                if(count($result['errors']) > 5) {
                    $msg .= "<li><i>...dan " . (count($result['errors']) - 5) . " error lainnya.</i></li>";
                }
                $msg .= "</ul>";
            }

            return redirect()->route('admin.students.index', ['school_id' => $schoolId])->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses CSV: ' . $e->getMessage());
        }
    }

    public function printCard($id)
    {
        $student = Student::with('classroom')->findOrFail($id);
        $user = Auth::user();
        
        if (!$user->hasRole('Super Admin') && $student->school_id !== $user->school_id) {
            abort(403, 'Akses ditolak.');
        }

        return view('admin.students.print', compact('student'));
    }

    public function bulkPrint(Request $request)
    {
        $user = Auth::user();
        $schoolId = $request->query('school_id') ?? $user->school_id;

        if (!$schoolId) {
            return back()->with('error', 'Pilih sekolah dari dropdown terlebih dahulu.');
        }

        $students = Student::with('classroom')->where('school_id', $schoolId)->get();
        
        if ($students->isEmpty()) {
            return back()->with('error', 'Belum ada data siswa di sekolah ini untuk dicetak.');
        }

        return view('admin.students.bulk_print', compact('students'));
    }
}