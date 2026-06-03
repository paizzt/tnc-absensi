@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Roster Jadwal Pelajaran</h4>
        <a href="{{ route('admin.schedules.create', ['school_id' => $selectedSchoolId ?? '']) }}" class="btn btn-primary btn-sm px-3">+ Buat Jadwal Baru</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
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
            <form action="{{ route('admin.schedules.index') }}" method="GET" class="d-flex align-items-center">
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