@extends('layouts.app')

@section('title', 'Pengaturan Sekolah')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Kustomisasi Aturan Sekolah</h4>
            <p class="text-neutral small mb-0">Atur jam operasional dan toleransi keterlambatan sistem absensi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #16A34A;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 max-w-4xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <h6 class="fw-bold mb-3" style="color: var(--primary);">1. Zona Waktu Sistem</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <select class="form-select @error('timezone') is-invalid @enderror" name="timezone" required>
                            <option value="Asia/Jakarta" {{ old('timezone', $setting->timezone) == 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Waktu Indonesia Barat)</option>
                            <option value="Asia/Makassar" {{ old('timezone', $setting->timezone) == 'Asia/Makassar' ? 'selected' : '' }}>WITA (Waktu Indonesia Tengah)</option>
                            <option value="Asia/Jayapura" {{ old('timezone', $setting->timezone) == 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Waktu Indonesia Timur)</option>
                        </select>
                        @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="mb-4 border-light">

                <h6 class="fw-bold mb-3" style="color: var(--primary);">2. Jam Operasional Harian</h6>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Jam Masuk</label>
                        <input type="time" class="form-control @error('time_in') is-invalid @enderror" name="time_in" value="{{ old('time_in', $setting->time_in) }}" required>
                        @error('time_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Batas Terlambat</label>
                        <input type="time" class="form-control @error('time_late') is-invalid @enderror" name="time_late" value="{{ old('time_late', $setting->time_late) }}" required>
                        @error('time_late')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-neutral small fw-semibold">Jam Pulang</label>
                        <input type="time" class="form-control @error('time_out') is-invalid @enderror" name="time_out" value="{{ old('time_out', $setting->time_out) }}" required>
                        @error('time_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="mb-4 border-light">

                <h6 class="fw-bold mb-3" style="color: var(--primary);">3. Parameter Poin Kedisiplinan BK</h6>
                <div class="row mb-5">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Maksimal Terlambat Ringan (Menit)</label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('late_light_max') is-invalid @enderror" name="late_light_max" value="{{ old('late_light_max', $setting->late_light_max) }}" required min="1">
                            <span class="input-group-text bg-light text-neutral small">Menit</span>
                            @error('late_light_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-text small text-neutral">Durasi keterlambatan yang akan dicatat sebagai poin ringan.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Maksimal Terlambat Sedang (Menit)</label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('late_medium_max') is-invalid @enderror" name="late_medium_max" value="{{ old('late_medium_max', $setting->late_medium_max) }}" required min="1">
                            <span class="input-group-text bg-light text-neutral small">Menit</span>
                            @error('late_medium_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-text small text-neutral">Lewat dari batas ini akan dihitung Terlambat Berat.</div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Simpan Konfigurasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection