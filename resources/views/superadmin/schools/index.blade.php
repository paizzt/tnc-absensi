@extends('layouts.app')

@section('title', 'Profil Sekolah')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Profil Sekolah</h4>
            <p class="text-neutral small mb-0">Kelola profil dan informasi kontak institusi sekolah.</p>
        </div>
        @role('Super Admin')
        <a href="{{ route('schools.create') }}" class="btn btn-primary btn-sm px-3 fw-medium shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Sekolah
        </a>
        @endrole
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('schools.index') }}" method="GET" class="d-flex align-items-center">
                <label class="fw-semibold text-primary me-3 mb-0" style="white-space: nowrap;">
                    <i class="bi bi-search me-1"></i> Cari Sekolah:
                </label>
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" class="form-control border-primary" name="search" placeholder="Masukkan NPSN atau Nama..." value="{{ request('search') }}">
                    <button class="btn btn-primary px-3" type="submit">Cari</button>
                </div>
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
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NPSN</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA SEKOLAH</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">KONTAK KAMI</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                        <tr>
                            <td class="px-4 py-3 fw-bold text-dark">{{ $school->npsn }}</td>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $school->name }}</td>
                            <td class="px-4 py-3 text-neutral small">
                                <div><i class="bi bi-envelope text-primary me-1"></i>{{ $school->email ?? 'Belum diatur' }}</div>
                                <div><i class="bi bi-telephone text-success me-1"></i>{{ $school->phone ?? 'Belum diatur' }}</div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('schools.edit', $school->id) }}" class="btn btn-sm btn-outline-warning text-dark" title="Edit Profil Sekolah">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    
                                    @role('Super Admin')
                                    <form action="{{ route('schools.destroy', $school->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Sekolah">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-5 text-center text-neutral">
                                <div class="mb-3"><i class="bi bi-buildings fs-1 text-opacity-50"></i></div>
                                <h6 class="fw-bold text-dark">Belum ada data sekolah.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($schools->hasPages())
                <div class="px-4 py-3 border-top bg-light">
                    {{ $schools->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection