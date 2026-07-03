@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Beranda Sistem</h4>
        <div class="text-neutral small">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);">
                <div class="card-body p-4 p-md-5 text-white position-relative">
                    <div style="position: relative; z-index: 2;">
                        <h3 class="fw-bold mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                        <p class="mb-0 opacity-75">
                            Anda masuk menggunakan peran: <strong>{{ Auth::user()->roles->pluck('name')->first() ?? 'Pengguna' }}</strong>
                            @if(Auth::user()->school)
                                | {{ Auth::user()->school->name }}
                            @endif
                        </p>
                    </div>
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1;"></div>
                    <div style="position: absolute; bottom: -80px; right: 50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; z-index: 1;"></div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3 text-dark">Akses Cepat</h5>
    <div class="row g-4">
        
        @role('Super Admin')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('schools.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow transition">
                    <div class="card-body p-4 text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-buildings"></i></div>
                        <h6 class="fw-bold text-dark mb-1">Master Sekolah</h6>
                        <p class="text-neutral small mb-0">Kelola pendaftaran sekolah baru.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('users.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow transition">
                    <div class="card-body p-4 text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-people"></i></div>
                        <h6 class="fw-bold text-dark mb-1">Data Pengguna</h6>
                        <p class="text-neutral small mb-0">Manajemen akun & hak akses.</p>
                    </div>
                </div>
            </a>
        </div>
        @endrole

        @hasanyrole('Super Admin|Admin Sekolah|Petugas Piket')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('admin.attendances.gate') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow transition" style="border-bottom: 4px solid var(--primary) !important;">
                    <div class="card-body p-4 text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-camera"></i></div>
                        <h6 class="fw-bold text-primary mb-1">Scanner Gerbang</h6>
                        <p class="text-neutral small mb-0">Buka pemindai absensi hari ini.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow transition">
                    <div class="card-body p-4 text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-mortarboard"></i></div>
                        <h6 class="fw-bold text-dark mb-1">Data Siswa</h6>
                        <p class="text-neutral small mb-0">Kelola siswa & cetak barcode.</p>
                    </div>
                </div>
            </a>
        </div>
        @endhasanyrole

        @hasanyrole('Super Admin|Guru')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('teacher.attendances.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow transition" style="border-bottom: 4px solid var(--success) !important;">
                    <div class="card-body p-4 text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-pencil-square"></i></div>
                        <h6 class="fw-bold text-success mb-1">Absensi Kelas</h6>
                        <p class="text-neutral small mb-0">Isi absensi jam pelajaran Anda.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('teacher.permissions.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow transition">
                    <div class="card-body p-4 text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-envelope"></i></div>
                        <h6 class="fw-bold text-dark mb-1">Validasi Izin</h6>
                        <p class="text-neutral small mb-0">Periksa pengajuan izin siswa.</p>
                    </div>
                </div>
            </a>
        </div>
        @endhasanyrole

        @hasanyrole('Super Admin|Guru BK|Kepala Sekolah')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('bk.dashboard') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 hover-shadow transition" style="border-bottom: 4px solid var(--danger) !important;">
                    <div class="card-body p-4 text-center">
                        <div class="fs-1 mb-2"><i class="bi bi-exclamation-triangle"></i></div>
                        <h6 class="fw-bold text-danger mb-1">Evaluasi Kehadiran</h6>
                        <p class="text-neutral small mb-0">Deteksi siswa bermasalah & SP.</p>
                    </div>
                </div>
            </a>
        </div>
        @endhasanyrole

    </div>
</div>
@endsection