@extends('layouts.app')

@section('title', 'Pengaturan Sekolah')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Pengaturan Sistem Sekolah</h4>
            <p class="text-neutral small mb-0">Atur jam operasional gerbang dan notifikasi WhatsApp.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light">
        <div class="card-body p-3">
            <form action="{{ route('admin.settings.index') }}" method="GET" class="d-flex align-items-center">
                <label class="fw-semibold text-primary me-3 mb-0" style="white-space: nowrap;">🏢 Filter Sekolah:</label>
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

    @if($setting)
    <div class="card border-0 shadow-sm rounded-4 max-w-3xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">

                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">⏰ Jam Operasional Absensi</h6>
                
                <div class="row mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Jam Masuk</label>
                        <input type="time" class="form-control" name="time_in" value="{{ $setting->time_in }}" required>
                        <div class="form-text small">Batas awal gerbang dibuka.</div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Batas Terlambat</label>
                        <input type="time" class="form-control border-warning" name="time_late" value="{{ $setting->time_late }}" required>
                        <div class="form-text small text-warning">Lewat jam ini = Terlambat.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-neutral small fw-semibold">Jam Pulang</label>
                        <input type="time" class="form-control" name="time_out" value="{{ $setting->time_out }}" required>
                        <div class="form-text small">Waktu pemindaian pulang dimulai.</div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 mt-5">📱 Notifikasi WhatsApp (Fonnte)</h6>
                
                <div class="mb-3">
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" role="switch" id="notify_in" name="notify_in" value="1" {{ $setting->notify_in ? 'checked' : '' }}>
                        <label class="form-check-label fs-6 ms-2 text-dark" for="notify_in">Kirim Notifikasi Absen Masuk</label>
                    </div>
                    <p class="text-neutral small ms-5 mb-0">Pesan WA akan dikirim ke orang tua saat siswa memindai QR Code di pagi hari.</p>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" role="switch" id="notify_out" name="notify_out" value="1" {{ $setting->notify_out ? 'checked' : '' }}>
                        <label class="form-check-label fs-6 ms-2 text-dark" for="notify_out">Kirim Notifikasi Absen Pulang</label>
                    </div>
                    <p class="text-neutral small ms-5 mb-0">Pesan WA akan dikirim ke orang tua saat siswa memindai QR Code untuk pulang.</p>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-5 fw-medium">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info border-0 shadow-sm">
        Silakan buat atau pilih sekolah terlebih dahulu untuk mengatur jam operasional.
    </div>
    @endif
</div>
@endsection