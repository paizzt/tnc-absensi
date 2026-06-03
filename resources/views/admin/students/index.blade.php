@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Manajemen Siswa</h4>
        <div>
            <button class="btn btn-success btn-sm px-3 me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                ⬇️ Import CSV
            </button>
            <a href="{{ route('admin.students.create', ['school_id' => $selectedSchoolId ?? '']) }}" class="btn btn-primary btn-sm px-3">+ Tambah Manual</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light">
        <div class="card-body p-3">
            <form action="{{ route('admin.students.index') }}" method="GET" class="d-flex align-items-center">
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

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NIS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA SISWA</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">KELAS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">WA ORTU</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">QR CODE</th>
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
                                <button class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $student->id }}">Lihat QR</button>
                                
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
                            <td colspan="5" class="px-4 py-5 text-center text-neutral">Belum ada data siswa.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top">{{ $students->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

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
                            <li>Pastikan penulisan <strong>Nama Kelas</strong> sama persis dengan yang ada di menu Master Kelas.</li>
                            <li>Simpan kembali dalam format <code>.csv</code> (Comma Delimited).</li>
                        </ol>
                        <div class="mt-3">
                            <a href="{{ route('admin.students.template') }}" class="btn btn-sm btn-outline-success fw-medium">📥 Download Template CSV</a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Unggah File CSV yang sudah diisi <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-white px-4 fw-medium">Proses Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection