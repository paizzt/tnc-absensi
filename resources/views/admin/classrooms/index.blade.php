@extends('layouts.app')

@section('title', 'Master Kelas')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Master Kelas</h4>
            <p class="text-neutral small mb-0">Kelola data kelas dan tugaskan Wali Kelas.</p>
        </div>
        <button class="btn btn-primary btn-sm px-3 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
        </button>
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
            <form action="{{ route('admin.classrooms.index') }}" method="GET" class="d-flex align-items-center">
                <label class="fw-semibold text-primary me-3 mb-0" style="white-space: nowrap;">
                    <i class="bi bi-buildings me-1"></i> Filter Sekolah:
                </label>
                <select name="school_id" class="form-select border-primary" onchange="this.form.submit()" style="max-width: 400px;">
                    <option value="">-- Pilih Sekolah --</option>
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
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">TINGKAT</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA KELAS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">WALI KELAS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classrooms as $class)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold">Tingkat {{ $class->level }}</span>
                            </td>
                            <td class="px-4 py-3 fw-bold text-dark">
                                {{ $class->name }}
                            </td>
                            <td class="px-4 py-3">
                                @if($class->homeroomTeacher)
                                    <div class="fw-medium text-dark"><i class="bi bi-person-badge text-primary me-1"></i> {{ $class->homeroomTeacher->name }}</div>
                                @else
                                    <span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i> Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-outline-warning text-dark me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $class->id }}" title="Edit Kelas">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('admin.classrooms.destroy', $class->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data kelas ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Kelas">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Kelas -->
                        <div class="modal fade" id="editModal{{ $class->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark">Edit Kelas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.classrooms.update', $class->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-semibold small">Tingkat Kelas</label>
                                                <input type="number" class="form-control" name="level" value="{{ $class->level }}" min="1" max="12" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-semibold small">Nama Kelas (Contoh: X MIPA 1)</label>
                                                <input type="text" class="form-control" name="name" value="{{ $class->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-semibold small">Wali Kelas (Opsional)</label>
                                                <select class="form-select" name="teacher_id">
                                                    <option value="">-- Kosongkan --</option>
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" {{ $class->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text small">Pilih guru yang bertugas mengelola kelas ini.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning fw-bold text-dark px-4"><i class="bi bi-save me-1"></i> Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-5 text-center">
                                <div class="text-neutral mb-3"><i class="bi bi-door-open fs-1 text-opacity-50"></i></div>
                                <h6 class="fw-bold text-dark">Belum Ada Data Kelas</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.classrooms.store') }}" method="POST">
                @csrf
                <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Tingkat Kelas</label>
                        <input type="number" class="form-control" name="level" placeholder="Contoh: 10" min="1" max="12" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Nama Kelas Lengkap</label>
                        <input type="text" class="form-control" name="name" placeholder="Contoh: X MIPA 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Wali Kelas (Opsional)</label>
                        <select class="form-select" name="teacher_id">
                            <option value="">-- Kosongkan --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text small">Pilih guru yang bertugas mengelola kelas ini.</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-plus-circle me-1"></i> Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection