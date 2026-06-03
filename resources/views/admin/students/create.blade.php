@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Form Input Siswa</h4>
            <p class="text-neutral small mb-0">Kode rahasia QR Code akan dibuat otomatis oleh sistem.</p>
        </div>
        <a href="{{ route('admin.students.index', ['school_id' => $schoolId]) }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 max-w-3xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.students.store') }}" method="POST">
                @csrf
                
                <!-- BLOK KHUSUS SUPER ADMIN -->
                @role('Super Admin')
                    <div class="mb-4 p-3 bg-light border rounded-3 border-primary border-opacity-25">
                        <label class="form-label text-primary small fw-semibold">🏢 Penempatan Sekolah (Mode Super Admin) <span class="text-danger">*</span></label>
                        <select class="form-select border-primary fw-medium" name="school_id" onchange="window.location.href='{{ route('admin.students.create') }}?school_id=' + this.value" required>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>
                                    {{ $school->npsn }} - {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-neutral">Mengubah sekolah akan memuat ulang daftar kelas di bawah ini secara otomatis.</div>
                    </div>
                @else
                    <!-- Input Tersembunyi untuk Admin Sekolah Biasa -->
                    <input type="hidden" name="school_id" value="{{ $schoolId }}">
                @endrole
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Nomor Induk Siswa (NIS) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nis') is-invalid @enderror" name="nis" value="{{ old('nis') }}" required>
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Pilih Kelas <span class="text-danger">*</span></label>
                        <select class="form-select @error('classroom_id') is-invalid @enderror" name="classroom_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $class)
                                <option value="{{ $class->id }}" {{ old('classroom_id') == $class->id ? 'selected' : '' }}>{{ $class->level }} - {{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('classroom_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($classrooms->isEmpty())
                            <div class="form-text text-danger small mt-1">Belum ada data kelas di sekolah ini. Silakan buat di menu Master Kelas terlebih dahulu.</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select @error('gender') is-invalid @enderror" name="gender" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label text-neutral small fw-semibold">Nomor WhatsApp Orang Tua <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">ID (+62/0)</span>
                        <input type="text" class="form-control border-start-0 @error('parent_phone') is-invalid @enderror" name="parent_phone" value="{{ old('parent_phone') }}" required placeholder="Contoh: 08123456789">
                    </div>
                    @error('parent_phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end border-top pt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-medium" {{ $classrooms->isEmpty() ? 'disabled' : '' }}>Simpan & Generate QR</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection