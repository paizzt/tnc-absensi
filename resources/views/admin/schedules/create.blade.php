@extends('layouts.app')

@section('title', 'Buat Jadwal Baru')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Penyusunan Jadwal</h4>
            <p class="text-neutral small mb-0">Sistem akan menolak otomatis jika terdeteksi jadwal bentrok.</p>
        </div>
        <a href="{{ route('admin.schedules.index', ['school_id' => $schoolId]) }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger" style="background-color: #fef2f2; border-color: #fecaca; color: #DC2626;">
            <strong>Gagal Menyimpan!</strong> {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 max-w-3xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.schedules.store') }}" method="POST">
                @csrf
                <input type="hidden" name="school_id" value="{{ $schoolId }}">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Pilih Kelas <span class="text-danger">*</span></label>
                        <select class="form-select @error('classroom_id') is-invalid @enderror" name="classroom_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $class)
                                <option value="{{ $class->id }}" {{ old('classroom_id') == $class->id ? 'selected' : '' }}>{{ $class->level }} - {{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('subject_id') is-invalid @enderror" name="subject_id" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-neutral small fw-semibold">Guru Pengajar <span class="text-danger">*</span></label>
                    <select class="form-select @error('teacher_id') is-invalid @enderror" name="teacher_id" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @if($teachers->isEmpty())
                        <div class="form-text text-danger small mt-1">Belum ada akun pengguna dengan Role "Guru Mata Pelajaran" di sekolah ini.</div>
                    @endif
                </div>

                <hr class="border-light mb-4">

                <div class="row mb-5">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Hari <span class="text-danger">*</span></label>
                        <select class="form-select @error('day_of_week') is-invalid @enderror" name="day_of_week" required>
                            <option value="">-- Hari --</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                <option value="{{ $hari }}" {{ old('day_of_week') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" name="start_time" value="{{ old('start_time') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-neutral small fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" name="end_time" value="{{ old('end_time') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection