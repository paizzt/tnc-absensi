@extends('layouts.app')

@section('title', 'Validasi Izin Siswa')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Daftar Validasi Izin</h4>
            <p class="text-neutral small mb-0">Permohonan izin sakit/kepentingan keluarga dari portal orang tua.</p>
        </div>
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
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">TANGGAL</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA SISWA</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">TIPE IZIN</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">STATUS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td class="px-4 py-3 text-neutral small">{{ \Carbon\Carbon::parse($req->date)->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="fw-medium text-dark">{{ $req->student->name }}</div>
                                <div class="text-neutral small">{{ $req->student->classroom->name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $req->type == 'Sakit' ? 'bg-warning' : 'bg-info' }} bg-opacity-75">{{ $req->type }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($req->status == 'Menunggu')
                                    <span class="badge bg-secondary bg-opacity-50 text-dark">Menunggu Validasi</span>
                                @elseif($req->status == 'Disetujui')
                                    <span class="badge bg-success bg-opacity-75">Disetujui</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-75">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('teacher.permissions.show', $req->id) }}" class="btn btn-sm btn-primary px-3">Tinjau Data</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-neutral">Belum ada permohonan izin yang masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top">{{ $requests->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection