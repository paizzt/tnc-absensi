<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CounselingService;
use Exception;

class CounselingController extends Controller
{
    protected $counselingService;

    public function __construct(CounselingService $counselingService)
    {
        $this->counselingService = $counselingService;
    }

    public function index()
    {
        $badStudents = $this->counselingService->getDashboardData();
        return view('bk.index', compact('badStudents'));
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
            return redirect()->route('bk.dashboard')->with('success', 'Surat Panggilan berhasil dikirim ke WhatsApp Orang Tua.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}