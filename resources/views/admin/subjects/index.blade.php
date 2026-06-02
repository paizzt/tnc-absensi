@extends('layouts.app')

@section('title', 'Master Mata Pelajaran')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Master Mata Pelajaran</h4>
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createModal">
            + Tambah Mapel
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #16A34A;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger" style="background-color: #fef2f2; border-color: #fecaca; color: #DC2626;">
            Terjadi kesalahan pada form. Pastikan data diisi dengan benar.
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">KODE</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA MATA PELAJARAN</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $sub)
                        <tr>
                            <td class="px-4 py-3 text-neutral">{{ $sub->code ?? '-' }}</td>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $sub->name }}</td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $sub->id }}">Edit</button>
                                    
                                    <form action="{{ route('admin.subjects.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Hapus mata pelajaran ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold">Edit Mata Pelajaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.subjects.update', $sub->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-neutral small fw-semibold">Kode Mapel (Opsional)</label>
                                                <input type="text" class="form-control" name="code" value="{{ $sub->code }}" placeholder="Contoh: MAT-01">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-neutral small fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="name" value="{{ $sub->name }}" required placeholder="Contoh: Matematika Wajib">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-5 text-center text-neutral">Belum ada data mata pelajaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top">{{ $subjects->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-neutral small fw-semibold">Kode Mapel (Opsional)</label>
                        <input type="text" class="form-control" name="code" placeholder="Contoh: MAT-01, B.IND">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-neutral small fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="Contoh: Matematika Wajib">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Mata Pelajaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection