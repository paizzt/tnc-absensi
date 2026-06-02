@extends('layouts.app')

@section('title', 'Jadwal Mengajar Hari Ini')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Jadwal Mengajar Hari Ini</h4>
            <p class="text-neutral small mb-0">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #16A34A;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($schedules as $sched)
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 hover-shadow transition">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                            {{ date('H:i', strtotime($sched->start_time)) }} - {{ date('H:i', strtotime($sched->end_time)) }}
                        </span>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">{{ $sched->classroom->name }}</h5>
                    <p class="text-neutral mb-4">{{ $sched->subject->name }}</p>
                    
                    <a href="{{ route('teacher.attendances.show', $sched->id) }}" class="btn btn-primary w-100 fw-medium">
                        Buka Absensi Kelas
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center text-neutral">
                    Anda tidak memiliki jadwal mengajar pada hari ini.
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection