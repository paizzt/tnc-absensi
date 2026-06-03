<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PermissionService;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Exception;

class PermissionApprovalController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $schools = [];
        $selectedSchoolId = null;

        if ($user->hasRole('Super Admin')) {
            $schools = School::orderBy('name')->get();
            $selectedSchoolId = $request->query('school_id') ?? ($schools->first()->id ?? null);
            $requests = $this->permissionService->getRequestsForCurrentSchool($selectedSchoolId);
        } else {
            $requests = $this->permissionService->getRequestsForCurrentSchool();
            $selectedSchoolId = $user->school_id;
        }

        return view('teacher.permissions.index', compact('requests', 'schools', 'selectedSchoolId'));
    }

    public function show($id)
    {
        $permission = $this->permissionService->getRequestById($id);
        return view('teacher.permissions.show', compact('permission'));
    }

    public function approve($id)
    {
        try {
            $this->permissionService->approveRequest($id);
            return back()->with('success', 'Permohonan berhasil disetujui dan absensi otomatis diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $this->permissionService->rejectRequest($id);
            return back()->with('success', 'Permohonan ditolak.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}