@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="fw-bold mb-1" style="color: #111827;">Export Laporan Absensi</h4>
        <p class="text-neutral small mb-0">Cetak dokumen PDF atau unduh data CSV untuk Microsoft Excel.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light">
        <div class="card-body p-3">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="d-flex align-items-center">
                <label class="fw-semibold text-primary me-3 mb-0"><i class="bi bi-buildings"></i> Filter Sekolah:</label>
                <select name="school_id" class="form-select border-primary" onchange="this.form.submit()" style="max-width: 400px;">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ ($selectedSchoolId == $school->id) ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    @endrole

    <div class="card border-0 shadow-sm rounded-4 max-w-3xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.reports.export') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-dark fw-semibold small">Dari Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-01') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small">Sampai Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-t') }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-dark fw-semibold small">Filter Kelas (Opsional)</label>
                        <select class="form-select" name="classroom_id">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classrooms as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text small">Kosongkan jika ingin menarik data seluruh sekolah.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small">Filter Mata Pelajaran (Opsional)</label>
                        <select class="form-select" name="subject_id">
                            <option value="">-- Semua Mapel --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text small">Pilih mapel tertentu jika ingin melihat absensi per mapel.</div>
                    </div>
                </div>

                <!-- PENGATURAN TANDA TANGAN DOKUMEN -->
                <div class="card border border-light-subtle rounded-3 mb-4 bg-light p-3">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pen me-1 text-primary"></i> Pengaturan Tanda Tangan Laporan (Bisa Diedit)</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="form-label text-dark fw-semibold small">Kota Penerbitan</label>
                            <input type="text" class="form-control form-control-sm" name="report_city" value="{{ $defaultCity ?? 'Makassar' }}" placeholder="Contoh: Makassar">
                            <div class="form-text small">Dicetak di atas tanda tangan sebelah kanan.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold small">Tanggal Dokumen</label>
                            <input type="date" class="form-control form-control-sm" name="report_date" value="{{ date('Y-m-d') }}">
                            <div class="form-text small">Tanggal pencetakan/penerbitan laporan.</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="form-label text-dark fw-semibold small">Nama Kepala Sekolah</label>
                            <input type="text" class="form-control form-control-sm" name="kepsek_name" value="{{ $defaultKepsekName }}" placeholder="Nama Kepala Sekolah">
                            <div class="form-text small">Otomatis terisi dari data Kepala Sekolah.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold small">NIP Kepala Sekolah</label>
                            <input type="text" class="form-control form-control-sm" name="kepsek_nip" value="{{ $defaultKepsekNip }}" placeholder="Contoh: 19780101 200501 1 001">
                            <div class="form-text small">Nomor Induk Pegawai Kepala Sekolah.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label text-dark fw-semibold small">Jabatan Penandatangan Kanan</label>
                            <input type="text" class="form-control form-control-sm" name="signer_title" value="Guru" placeholder="Contoh: Guru / Wali Kelas">
                            <div class="form-text small">Bisa diisi Guru, Wali Kelas, dll.</div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label text-dark fw-semibold small">Nama Guru / Penandatangan</label>
                            <input type="text" class="form-control form-control-sm" name="exporter_name" value="{{ $defaultExporterName }}" placeholder="Nama Guru">
                            <div class="form-text small">Otomatis terisi dari akun saat ini.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">NIP Guru / Penandatangan</label>
                            <input type="text" class="form-control form-control-sm" name="exporter_nip" value="{{ $defaultExporterNip }}" placeholder="Contoh: 19850502 201001 2 003">
                            <div class="form-text small">Nomor Induk Pegawai Guru.</div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label text-dark fw-semibold small">Format Cetak <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check border rounded p-3 flex-fill bg-light">
                            <input class="form-check-input ms-1" type="radio" name="format" id="f_pdf" value="pdf" checked>
                            <label class="form-check-label ms-2 fw-medium" for="f_pdf">
                                <i class="bi bi-file-earmark-text"></i> Dokumen PDF (Cetak)
                            </label>
                        </div>
                        <div class="form-check border rounded p-3 flex-fill bg-light">
                            <input class="form-check-input ms-1" type="radio" name="format" id="f_csv" value="csv">
                            <label class="form-check-label ms-2 fw-medium" for="f_csv">
                                <i class="bi bi-bar-chart"></i> File Excel (CSV)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 pt-2">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary py-2 w-100 fw-bold" {{ !$selectedSchoolId ? 'disabled' : '' }}>
                            <i class="bi bi-download me-1"></i> Buat Laporan (PDF / CSV)
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" 
                                formaction="{{ route('admin.reports.send_custom_email') }}" 
                                formtarget="_self" 
                                class="btn btn-success py-2 w-100 fw-bold" 
                                {{ !$selectedSchoolId || ($studentWithEmailCount ?? 0) === 0 ? 'disabled' : '' }}
                                onclick="return confirm('Kirimkan rekap laporan kehadiran untuk periode tanggal dan kelas yang dipilih ke seluruh siswa ber-email?')">
                            <i class="bi bi-envelope-check-fill me-1"></i> Kirim Laporan via Email (Periode Ini)
                        </button>
                    </div>
                </div>
                <div class="form-text small mt-2">
                    <i class="bi bi-info-circle text-primary me-1"></i> Klik <strong>Buat Laporan</strong> untuk mencetak/mengunduh file, atau <strong>Kirim Laporan via Email</strong> untuk mengirim rekap absensi rentang tanggal yang dipilih langsung ke email/Gmail siswa.
                </div>
            </form>
        </div>
    </div>

    <!-- KARTU PENGIRIMAN LAPORAN MINGGUAN KE EMAIL / GMAIL -->
    <div class="card border-0 shadow-sm rounded-4 max-w-3xl mt-4">
        <div class="card-body p-4 p-md-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-envelope-paper text-primary me-2"></i>Kirim Laporan Mingguan ke Email Siswa / Orang Tua</h6>
                    <p class="text-neutral small mb-0">Laporan mingguan dijadwalkan otomatis tiap hari Sabtu pukul 18:00, atau kirim manual sekarang.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                    <i class="bi bi-people-fill me-1"></i> {{ $studentWithEmailCount ?? 0 }} Siswa Ber-email
                </span>
            </div>

            <form action="{{ route('admin.reports.send_weekly_email') }}" method="POST" onsubmit="return confirm('Kirimkan rekap laporan mingguan via Gmail/email ke seluruh siswa yang memiliki email?')">
                @csrf
                <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-outline-primary btn-sm px-4 fw-bold" {{ ($studentWithEmailCount ?? 0) === 0 ? 'disabled' : '' }}>
                        <i class="bi bi-send-fill me-1"></i> Kirim Laporan Mingguan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection