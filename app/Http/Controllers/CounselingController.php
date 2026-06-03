<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CounselingService;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Exception;

class CounselingController extends Controller
{
    protected $counselingService;

    public function __construct(CounselingService $counselingService)
    {
        $this->counselingService = $counselingService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
            $badStudents = $this->counselingService->getDashboardData($selectedSchoolId);
        } else {
            $badStudents = $this->counselingService->getDashboardData();
            $selectedSchoolId = $user->school_id;
        }

        return view('bk.index', compact('badStudents', 'schools', 'selectedSchoolId'));
    }

    public function sendSp(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'sp_level' => 'required|integer|in:1,2,3',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $this->counselingService->sendWarningLetter($request->all(), $request->file('document'));
            
            // Redirect dengan membawa query string filter jika ada
            $schoolId = $request->input('school_id');
            return redirect()->route('bk.dashboard', ['school_id' => $schoolId])
                ->with('success', 'Surat Panggilan berhasil dikirim ke WhatsApp Orang Tua.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}