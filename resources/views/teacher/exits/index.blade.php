@extends('layouts.app')

@section('title', 'Izin Keluar Sementara')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold mb-1 text-dark">Izin Keluar (Gate Pass)</h4>
            <p class="text-neutral small mb-0">Kelola izin keluar area sekolah untuk siswa kelas <strong>{{ $classroom->name }}</strong>.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#createExitModal">
                <i class="bi bi-plus-lg me-1"></i> Buat Surat Izin
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Izin Keluar Hari Ini</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">WAKTU DIBUAT</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA SISWA</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">ALASAN</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">BATAS WAKTU</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">STATUS SAAT INI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exits as $exit)
                        <tr>
                            <td class="px-4 py-3 text-dark small">{{ $exit->created_at->format('H:i') }} WITA</td>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $exit->student->name }}</td>
                            <td class="px-4 py-3 text-neutral small">{{ $exit->reason }}</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-light text-dark border"><i class="bi bi-stopwatch text-danger me-1"></i> {{ $exit->valid_until->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($exit->status == 'Disetujui')
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-person-walking me-1"></i> Menuju Gerbang</span>
                                @elseif($exit->status == 'Keluar')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-2 py-1"><i class="bi bi-sign-turn-right me-1"></i> Sedang di Luar</span>
                                @elseif($exit->status == 'Kembali')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-check2-all me-1"></i> Telah Kembali</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-exclamation-octagon me-1"></i> Terlambat Kembali</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-neutral">
                                <i class="bi bi-inbox fs-1 text-opacity-50 d-block mb-2"></i>
                                Belum ada siswa yang meminta izin keluar hari ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createExitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Terbitkan Izin Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teacher.exits.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Pilih Siswa</label>
                        <select class="form-select" name="student_id" required>
                            <option value="">-- Cari Nama Siswa --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->nis }} - {{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Alasan Keluar Sementara</label>
                        <input type="text" class="form-control" name="reason" placeholder="Contoh: Membeli alat tulis, Fotokopi tugas..." required maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Durasi Izin</label>
                        <select class="form-select" name="duration_minutes" required>
                            <option value="15">15 Menit</option>
                            <option value="30">30 Menit</option>
                            <option value="45">45 Menit</option>
                            <option value="60">1 Jam</option>
                            <option value="120">2 Jam</option>
                        </select>
                        <div class="form-text small">Sistem akan menandai terlambat jika siswa kembali melewati durasi ini.</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="bi bi-send me-1"></i> Terbitkan Izin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection