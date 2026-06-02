@extends('layouts.app')

@section('title', 'Master Sekolah')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Data Master Sekolah</h4>
        <a href="{{ route('schools.create') }}" class="btn btn-primary btn-sm px-3">
            + Tambah Sekolah
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #16A34A;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NPSN</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA SEKOLAH</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">KONTAK</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                        <tr>
                            <td class="px-4 py-3">{{ $school->npsn }}</td>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $school->name }}</td>
                            <td class="px-4 py-3 text-neutral small">
                                <div>{{ $school->email ?? '-' }}</div>
                                <div>{{ $school->phone ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('schools.edit', $school->id) }}" class="btn btn-sm btn-light border text-primary">Edit</a>
                                    
                                    <form action="{{ route('schools.destroy', $school->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-5 text-center text-neutral">
                                Belum ada data sekolah yang terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-top">
                {{ $schools->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection