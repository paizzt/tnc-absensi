<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PermissionService;
use Exception;

class PermissionApprovalController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $requests = $this->permissionService->getRequestsForCurrentSchool();
        return view('teacher.permissions.index', compact('requests'));
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
            return redirect()->route('teacher.permissions.index')->with('success', 'Permohonan berhasil disetujui dan absensi otomatis diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $this->permissionService->rejectRequest($id);
            return redirect()->route('teacher.permissions.index')->with('success', 'Permohonan ditolak.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}