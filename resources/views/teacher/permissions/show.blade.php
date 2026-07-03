@extends('layouts.app')

@section('title', 'Tinjau Izin')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Validasi Dokumen & Keamanan</h4>
            <p class="text-neutral small mb-0">Pastikan wajah pada foto sinkron dengan identitas pengirim.</p>
        </div>
        <a href="{{ route('teacher.permissions.index') }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <span class="badge {{ $permission->type == 'Sakit' ? 'bg-warning' : 'bg-info' }} bg-opacity-75 px-3 py-2 fs-6">{{ $permission->type }}</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $permission->student->name }}</h5>
                            <p class="text-muted small mb-0">{{ $permission->student->classroom->name }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-neutral small fw-semibold">Alasan Keterangan:</label>
                        <div class="p-3 bg-light rounded-3 border">
                            {{ $permission->reason }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-neutral small fw-semibold">Foto Selfie Verifikasi (Diambil Otomatis):</label>
                        <div class="text-center bg-dark rounded-3 overflow-hidden" style="max-height: 400px;">
                            <img src="{{ asset('storage/' . $permission->selfie_path) }}" alt="Foto Verifikasi" class="img-fluid" style="object-fit: contain; width: 100%; height: 100%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Lampiran Bukti (Opsional)</h6>
                    @if($permission->document_path)
                        <a href="{{ asset('storage/' . $permission->document_path) }}" target="_blank" class="btn btn-outline-primary w-100">
                            <i class="bi bi-file-earmark-text"></i> Lihat Dokumen Terlampir
                        </a>
                    @else
                        <div class="p-4 text-center bg-light border border-dashed rounded text-neutral small">
                            Tidak ada dokumen yang dilampirkan.
                        </div>
                    @endif
                </div>
            </div>

            @if($permission->status == 'Menunggu')
            <div class="card border-0 shadow-sm rounded-4 border-start border-4 border-primary">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Tindakan Wali Kelas</h6>
                    <p class="small text-neutral mb-4">Dengan menyetujui, absensi gerbang siswa hari ini akan otomatis diisi sesuai tipe halangan.</p>
                    
                    <div class="d-grid gap-2">
                        <form action="{{ route('teacher.permissions.approve', $permission->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 fw-medium"><i class="bi bi-check-circle"></i> Setujui & Rekam Absen</button>
                        </form>
                        
                        <form action="{{ route('teacher.permissions.reject', $permission->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak permohonan ini?');">
                            @csrf
                            <button type="submit" class="btn btn-danger text-white bg-opacity-75 w-100 fw-medium border-0"><i class="bi bi-x-circle"></i> Tolak Permohonan</button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold text-dark mb-2">Status Saat Ini</h6>
                    @if($permission->status == 'Disetujui')
                        <span class="badge bg-success px-4 py-2 fs-6">Telah Disetujui</span>
                    @else
                        <span class="badge bg-danger px-4 py-2 fs-6">Ditolak</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection