@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Edit Data Siswa</h4>
            <p class="text-neutral small mb-0">Perbarui informasi siswa di bawah ini.</p>
        </div>
        <a href="{{ route('admin.students.index', ['school_id' => $student->school_id]) }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 max-w-3xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Nomor Induk Siswa (NIS) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nis') is-invalid @enderror" name="nis" value="{{ old('nis', $student->nis) }}" required>
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $student->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Pilih Kelas <span class="text-danger">*</span></label>
                        <select class="form-select @error('classroom_id') is-invalid @enderror" name="classroom_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $class)
                                <option value="{{ $class->id }}" {{ old('classroom_id', $student->classroom_id) == $class->id ? 'selected' : '' }}>{{ $class->level }} - {{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('classroom_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select @error('gender') is-invalid @enderror" name="gender" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Nomor WhatsApp Orang Tua <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">ID (+62/0)</span>
                            <input type="text" class="form-control border-start-0 @error('parent_phone') is-invalid @enderror" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" required placeholder="Contoh: 08123456789">
                        </div>
                        @error('parent_phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Email Orang Tua (Opsional)</label>
                        <input type="email" class="form-control @error('parent_email') is-invalid @enderror" name="parent_email" value="{{ old('parent_email', $student->parent_email) }}" placeholder="Contoh: email@domain.com">
                        @error('parent_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
