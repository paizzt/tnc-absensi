@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <h4 class="fw-bold mb-1" style="color: #111827;">Export Laporan Absensi</h4>
        <p class="text-neutral small mb-0">Cetak dokumen PDF atau unduh data CSV untuk Microsoft Excel.</p>
    </div>

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

                <div class="mb-4">
                    <label class="form-label text-dark fw-semibold small">Filter Kelas (Opsional)</label>
                    <select class="form-select" name="classroom_id">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classrooms as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text small">Kosongkan jika ingin menarik data seluruh sekolah.</div>
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

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2 fw-bold" {{ !$selectedSchoolId ? 'disabled' : '' }}>Buat Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection