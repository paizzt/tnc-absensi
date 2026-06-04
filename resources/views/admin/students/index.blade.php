@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Manajemen Siswa</h4>
            <p class="text-neutral small mb-0">Kelola data, cetak ID Card (QR), atau impor dari Excel.</p>
        </div>
        <div>
            <a href="{{ route('admin.students.bulk_print', ['school_id' => $selectedSchoolId ?? '']) }}" class="btn btn-warning btn-sm px-3 me-2 fw-medium text-dark shadow-sm">
                <i class="bi bi-file-earmark-zip-fill me-1"></i> Cetak Massal (ZIP)
            </a>
            <button class="btn btn-success btn-sm px-3 me-2 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Import CSV
            </button>
            <a href="{{ route('admin.students.create', ['school_id' => $selectedSchoolId ?? '']) }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Manual
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- DROPDOWN KHUSUS SUPER ADMIN -->
    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.students.index') }}" method="GET" class="d-flex align-items-center">
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

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NIS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA SISWA</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">KELAS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">WA ORTU</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">AKSI & CETAK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $student->nis }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $student->name }}</div>
                                <small class="text-neutral">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                            </td>
                            <td class="px-4 py-3 text-neutral">{{ $student->classroom->name }}</td>
                            <td class="px-4 py-3 text-neutral">{{ $student->parent_phone }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Tombol Lihat QR Sementara -->
                                    <button class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $student->id }}" title="Lihat QR Code">
                                        <i class="bi bi-qr-code"></i> Lihat
                                    </button>
                                    
                                    <!-- Tombol Cetak ID Card Satuan -->
                                    <a href="{{ route('admin.students.print_card', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Cetak ID Card Barcode">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                                
                                <!-- Modal QR Code (Preview) -->
                                <div class="modal fade" id="qrModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-body text-center p-4">
                                                <h6 class="fw-bold text-dark mb-1">{{ $student->name }}</h6>
                                                <p class="small text-neutral mb-3">{{ $student->nis }} - {{ $student->classroom->name }}</p>
                                                
                                                <div class="bg-white p-2 d-inline-block border rounded mb-3">
                                                    {!! QrCode::size(150)->generate($student->qr_code_string) !!}
                                                </div>
                                                
                                                <div class="d-grid">
                                                    <button type="button" class="btn btn-light border text-neutral btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-neutral">
                                <i class="bi bi-inbox fs-1 text-opacity-50"></i>
                                <p class="mt-2">Belum ada data siswa. Silakan import dari CSV atau tambah manual.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="px-4 py-3 border-top bg-light">{{ $students->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Import Data Siswa (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                
                <div class="modal-body">
                    <div class="mb-4 bg-light p-3 rounded border">
                        <h6 class="fw-bold text-primary mb-2">Panduan Import:</h6>
                        <ol class="small text-neutral mb-0 ps-3">
                            <li>Unduh template CSV di bawah ini.</li>
                            <li>Buka dengan Excel/Spreadsheet.</li>
                            <li>Pastikan penulisan <strong>Nama Kelas</strong> sesuai.</li>
                            <li>Simpan kembali dalam format <code>.csv</code> (Comma Delimited).</li>
                        </ol>
                        <div class="mt-3">
                            <a href="{{ route('admin.students.template') }}" class="btn btn-sm btn-outline-success fw-medium">
                                <i class="bi bi-download me-1"></i> Download Template
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Unggah File CSV <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-white px-4 fw-medium"><i class="bi bi-upload me-1"></i> Proses Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection