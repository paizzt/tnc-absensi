@extends('layouts.app')

@section('title', 'Manajemen Iklan Banner')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Banner Iklan Slide</h4>
            <p class="text-neutral small mb-0">Kelola gambar iklan yang akan tampil di halaman Login.</p>
        </div>
        <a href="{{ route('banners.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Banner
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">Urutan</th>
                            <th width="30%">Preview Gambar</th>
                            <th width="30%">Judul (Opsional)</th>
                            <th width="15%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td><span class="badge bg-secondary rounded-circle px-2 py-2">{{ $banner->order }}</span></td>
                                <td>
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="img-thumbnail" style="max-height: 80px; object-fit: cover;">
                                </td>
                                <td class="fw-medium">
                                    {{ $banner->title ?: '-' }}
                                    @if($banner->link)
                                        <div class="small mt-1"><a href="{{ $banner->link }}" target="_blank" class="text-decoration-none text-primary"><i class="bi bi-link-45deg"></i> {{ Str::limit($banner->link, 30) }}</a></div>
                                    @endif
                                </td>
                                <td>
                                    @if($banner->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-neutral">
                                    <i class="bi bi-images fs-1 d-block mb-3 opacity-50"></i>
                                    Belum ada banner iklan yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
