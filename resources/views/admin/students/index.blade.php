@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Manajemen Siswa</h4>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm px-3">+ Tambah Siswa</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #16A34A;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                                <button class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $student->id }}">
                                    Lihat QR
                                </button>
                                
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
@endsection