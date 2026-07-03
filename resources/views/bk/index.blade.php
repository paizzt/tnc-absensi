@extends('layouts.app')

@section('title', 'Evaluasi Kehadiran & Surat Panggilan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Deteksi Dini Kehadiran Buruk</h4>
            <p class="text-neutral small mb-0">Sistem otomatis memfilter siswa dengan persentase absensi di bawah 80%.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}</div>
    @endif

    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light">
        <div class="card-body p-3">
            <form action="{{ route('bk.dashboard') }}" method="GET" class="d-flex align-items-center">
                <label class="fw-semibold text-primary me-3 mb-0" style="white-space: nowrap;"><i class="bi bi-buildings"></i> Filter Sekolah:</label>
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

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA SISWA</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">PERSENTASE</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">TOTAL ALPHA/BOLOS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">STATUS SP</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($badStudents as $student)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $student->name }}</div>
                                <div class="text-neutral small">{{ $student->nis }} • {{ $student->classroom->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-danger px-3 py-2 fs-6">{{ $student->attendance_percentage }}%</span>
                            </td>
                            <td class="px-4 py-3 text-center fw-medium text-danger">
                                {{ $student->total_alpha }} Hari
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($student->last_sp > 0)
                                    <span class="badge bg-warning text-dark bg-opacity-75">Telah SP-{{ $student->last_sp }}</span>
                                @else
                                    <span class="badge bg-light text-neutral border">Belum SP</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#spModal{{ $student->id }}">Kirim SP</button>
                                
                                <div class="modal fade text-start" id="spModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-dark">Kirim Surat Panggilan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('bk.send_sp') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                
                                                <div class="modal-body">
                                                    <p class="small text-neutral mb-4">Sistem akan mengirimkan pesan notifikasi dan tautan berkas SP langsung ke WhatsApp orang tua <strong>{{ $student->name }}</strong>.</p>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label text-dark small fw-semibold">Tingkat SP <span class="text-danger">*</span></label>
                                                        <select class="form-select" name="sp_level" required>
                                                            <option value="1" {{ $student->last_sp == 0 ? 'selected' : '' }}>Surat Peringatan 1 (SP-1)</option>
                                                            <option value="2" {{ $student->last_sp == 1 ? 'selected' : '' }}>Surat Peringatan 2 (SP-2)</option>
                                                            <option value="3" {{ $student->last_sp >= 2 ? 'selected' : '' }}>Surat Peringatan 3 (SP-3)</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label text-dark small fw-semibold">Unggah Draf SP (PDF/JPG) <span class="text-danger">*</span></label>
                                                        <input type="file" class="form-control" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger text-white">Kirim via WhatsApp</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-success fw-medium">
                                Luar biasa! Seluruh siswa di sekolah yang dipilih memiliki kehadiran yang baik.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection