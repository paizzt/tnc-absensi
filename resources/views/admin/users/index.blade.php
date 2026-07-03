@extends('layouts.app')

@section('title', 'Data Pengguna')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Data Pengguna</h4>
            <p class="text-neutral small mb-0">Kelola akun Admin, Petugas Piket, Wali Kelas, dan Guru.</p>
        </div>
        <a href="{{ route('users.create', ['school_id' => $selectedSchoolId ?? '']) }}" class="btn btn-primary btn-sm px-3 fw-medium shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- KUNCI GEMBOK: HANYA SUPER ADMIN YANG BISA MELIHAT FILTER INI -->
    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('users.index') }}" method="GET" class="d-flex align-items-center">
                <label class="fw-semibold text-primary me-3 mb-0" style="white-space: nowrap;">
                    <i class="bi bi-buildings me-1"></i> Filter Sekolah:
                </label>
                <select name="school_id" class="form-select border-primary" onchange="this.form.submit()" style="max-width: 400px;">
                    <option value="">-- Semua Sekolah --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ ($selectedSchoolId == $school->id) ? 'selected' : '' }}>
                            {{ $school->npsn }} - {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    @endrole

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA & EMAIL</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">PERAN (ROLE)</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">PENUGASAN AKADEMIK</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                                <div class="small text-neutral"><i class="bi bi-envelope me-1"></i>{{ $u->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @foreach($u->roles as $role)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">
                                @if($u->hasRole('Guru'))
                                    <div class="small">
                                        <span class="text-dark fw-medium">Wali Kelas:</span> 
                                        @if($u->homeroomClass)
                                            <span class="text-success fw-bold">{{ $u->homeroomClass->name }}</span>
                                        @else
                                            <span class="text-neutral">-</span>
                                        @endif
                                    </div>
                                    <div class="small mt-1">
                                        <span class="text-dark fw-medium">Mengajar:</span> 
                                        @if($u->subjects && $u->subjects->count() > 0)
                                            <span class="text-primary fw-medium">{{ $u->subjects->count() }} Mapel</span>
                                        @else
                                            <span class="text-neutral">Belum ada mapel</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-neutral small">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('users.edit', $u->id) }}" class="btn btn-sm btn-outline-warning text-dark me-1" title="Edit Pengguna">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if(Auth::id() !== $u->id)
                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun pengguna ini secara permanen?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Pengguna">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-5 text-center text-neutral">
                                <div class="mb-3"><i class="bi bi-people fs-1 text-opacity-50"></i></div>
                                <h6 class="fw-bold text-dark">Belum Ada Data Pengguna</h6>
                                <p class="small text-neutral mb-0">Silakan tambahkan akun pengguna baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-4 py-3 border-top bg-light">{{ $users->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection