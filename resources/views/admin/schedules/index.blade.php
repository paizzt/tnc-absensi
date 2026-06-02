@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Roster Jadwal Pelajaran</h4>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm px-3">+ Buat Jadwal Baru</a>
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
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">HARI & WAKTU</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">KELAS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">MATA PELAJARAN</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">GURU PENGAJAR</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $sched)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-primary">{{ strtoupper($sched->day_of_week) }}</div>
                                <div class="text-neutral small">{{ date('H:i', strtotime($sched->start_time)) }} - {{ date('H:i', strtotime($sched->end_time)) }}</div>
                            </td>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $sched->classroom->name }}</td>
                            <td class="px-4 py-3 text-neutral">{{ $sched->subject->name }}</td>
                            <td class="px-4 py-3 text-neutral">{{ $sched->teacher->name }}</td>
                            <td class="px-4 py-3 text-end">
                                <form action="{{ route('admin.schedules.destroy', $sched->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-neutral">Belum ada roster jadwal pelajaran yang disusun.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top">{{ $schedules->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection