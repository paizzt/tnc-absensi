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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentExitController;

Route::get('/', function () { return redirect()->route('login'); });

// ==========================================
// AREA PUBLIK (PORTAL SISWA & ORANG TUA)
// ==========================================
Route::prefix('portal/izin')->name('portal.izin.')->group(function () {
    Route::get('/', [ParentPortalController::class, 'index'])->name('index');
    Route::post('/search', [ParentPortalController::class, 'search'])->name('search');
    Route::get('/form/{student_id}', [ParentPortalController::class, 'form'])->name('form');
    Route::post('/submit/{student_id}', [ParentPortalController::class, 'submit'])->name('submit');
});

// ==========================================
// AUTENTIKASI (LOGIN & LOGOUT)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});

// ==========================================
// AREA DALAM SISTEM (SETELAH LOGIN)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // AREA SUPER ADMIN
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::resource('schools', SchoolController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });

    // PENGATURAN SEKOLAH (Akses Diperluas ke BK & Kepala Sekolah)
    Route::middleware(['role:Super Admin|Admin Sekolah|Petugas Piket|Guru BK|Kepala Sekolah'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [SchoolSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SchoolSettingController::class, 'update'])->name('settings.update');
    });

    // AREA ADMIN SEKOLAH & PETUGAS PIKET
    Route::middleware(['role:Super Admin|Admin Sekolah|Petugas Piket'])->prefix('admin')->name('admin.')->group(function () {
        // Master Data
        Route::resource('classrooms', ClassroomController::class)->except(['show', 'create', 'edit']);
        Route::resource('subjects', SubjectController::class)->except(['show', 'create', 'edit']);
        
        // Rute Laporan
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        
        // Manajemen Siswa (Import & Cetak ID Card)
        Route::get('/students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('/students/bulk-print', [StudentController::class, 'bulkPrint'])->name('students.bulk_print');
        Route::get('/students/{id}/print', [StudentController::class, 'printCard'])->name('students.print_card');
        Route::resource('students', StudentController::class)->except(['show', 'edit', 'update', 'destroy']);
        
        // Manajemen Roster / Jadwal Pelajaran
        Route::delete('/schedules/class/{classroom}', [ScheduleController::class, 'destroyClass'])->name('schedules.destroy_class');
        Route::resource('schedules', ScheduleController::class)->except(['show']);
        
        // Absensi Gerbang Live
        Route::get('/scan', [GateAttendanceController::class, 'index'])->name('attendances.gate');
        Route::post('/scan/process', [GateAttendanceController::class, 'scan'])->name('attendances.scan_process');
    });

    // AREA GURU (WALI KELAS & GURU MATA PELAJARAN)
    Route::middleware(['role:Super Admin|Guru Mata Pelajaran|Wali Kelas'])->prefix('teacher')->name('teacher.')->group(function () {
        // Absensi Kelas
        Route::get('/attendances', [TeacherAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/attendances/{schedule}', [TeacherAttendanceController::class, 'show'])->name('attendances.show');
        Route::post('/attendances/{schedule}', [TeacherAttendanceController::class, 'store'])->name('attendances.store');
        
        // Validasi Izin Siswa (Dari Portal Publik)
        Route::get('/permissions', [PermissionApprovalController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{id}', [PermissionApprovalController::class, 'show'])->name('permissions.show');
        Route::post('/permissions/{id}/approve', [PermissionApprovalController::class, 'approve'])->name('permissions.approve');
        Route::post('/permissions/{id}/reject', [PermissionApprovalController::class, 'reject'])->name('permissions.reject');

        // Izin Keluar Sementara / Gate Pass (Khusus Wali Kelas)
        Route::get('/exits', [StudentExitController::class, 'index'])->name('exits.index');
        Route::post('/exits', [StudentExitController::class, 'store'])->name('exits.store');
    });

    // AREA BK & KEPALA SEKOLAH
    Route::middleware(['role:Super Admin|Guru BK|Kepala Sekolah'])->prefix('bk')->name('bk.')->group(function () {
        Route::get('/dashboard', [CounselingController::class, 'index'])->name('dashboard');
        Route::post('/send-sp', [CounselingController::class, 'sendSp'])->name('send_sp');
    });
});