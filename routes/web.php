<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GateAttendanceController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\PermissionApprovalController;
use App\Http\Controllers\CounselingController;

Route::get('/', function () { return redirect()->route('login'); });

Route::prefix('portal/izin')->name('portal.izin.')->group(function () {
    Route::get('/', [ParentPortalController::class, 'index'])->name('index');
    Route::post('/search', [ParentPortalController::class, 'search'])->name('search');
    Route::get('/form/{student_id}', [ParentPortalController::class, 'form'])->name('form');
    Route::post('/submit/{student_id}', [ParentPortalController::class, 'submit'])->name('submit');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // AREA SUPER ADMIN
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::resource('schools', SchoolController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });

    // AREA ADMIN SEKOLAH / PETUGAS PIKET
    Route::middleware(['role:Admin Sekolah|Petugas Piket'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [SchoolSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SchoolSettingController::class, 'update'])->name('settings.update');
        Route::resource('classrooms', ClassroomController::class)->except(['show', 'create', 'edit']);
        Route::resource('subjects', SubjectController::class)->except(['show', 'create', 'edit']);
        Route::resource('students', StudentController::class)->except(['show', 'edit', 'update', 'destroy']);
        Route::resource('schedules', ScheduleController::class)->except(['show', 'edit', 'update']);
        Route::get('/scan', [GateAttendanceController::class, 'index'])->name('attendances.gate');
        Route::post('/scan/process', [GateAttendanceController::class, 'scan'])->name('attendances.scan_process');
    });

    // AREA GURU (PORTAL GURU)
    Route::middleware(['role:Guru Mata Pelajaran|Wali Kelas'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/attendances', [TeacherAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/{schedule}', [TeacherAttendanceController::class, 'show'])->name('attendances.show');
        Route::post('/attendances/{schedule}', [TeacherAttendanceController::class, 'store'])->name('attendances.store');
        Route::get('/permissions', [PermissionApprovalController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{id}', [PermissionApprovalController::class, 'show'])->name('permissions.show');
        Route::post('/permissions/{id}/approve', [PermissionApprovalController::class, 'approve'])->name('permissions.approve');
        Route::post('/permissions/{id}/reject', [PermissionApprovalController::class, 'reject'])->name('permissions.reject');
    });

    // AREA GURU BK & KEPALA SEKOLAH
    Route::middleware(['role:Guru BK|Kepala Sekolah'])->prefix('bk')->name('bk.')->group(function () {
        Route::get('/dashboard', [CounselingController::class, 'index'])->name('dashboard');
        Route::post('/send-sp', [CounselingController::class, 'sendSp'])->name('send_sp');
    });
});