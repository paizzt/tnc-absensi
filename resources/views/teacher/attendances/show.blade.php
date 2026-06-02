@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Form Presensi Siswa</h4>
            <p class="text-neutral small mb-0">{{ $schedule->classroom->name }} | {{ $schedule->subject->name }}</p>
        </div>
        <a href="{{ route('teacher.attendances.index') }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <form action="{{ route('teacher.attendances.store', $schedule->id) }}" method="POST">
            @csrf
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm w-25">NAMA SISWA</th>
                                <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">HADIR</th>
                                <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">SAKIT</th>
                                <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">IZIN</th>
                                <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-center">ALPHA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            @php
                                // Ambil status jika sudah pernah diabsen hari ini, default 'Hadir'
                                $currentStatus = isset($records[$student->id]) ? $records[$student->id]->status : 'Hadir';
                            @endphp
                            <tr>
                                <td class="px-4 py-3 fw-medium text-dark">{{ $student->name }}</td>
                                <td class="px-4 py-3 text-center">
                                    <input class="form-check-input fs-5" type="radio" name="attendance[{{ $student->id }}]" value="Hadir" {{ $currentStatus == 'Hadir' ? 'checked' : '' }}>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input class="form-check-input fs-5" type="radio" name="attendance[{{ $student->id }}]" value="Sakit" {{ $currentStatus == 'Sakit' ? 'checked' : '' }}>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input class="form-check-input fs-5" type="radio" name="attendance[{{ $student->id }}]" value="Izin" {{ $currentStatus == 'Izin' ? 'checked' : '' }}>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input class="form-check-input fs-5" type="radio" name="attendance[{{ $student->id }}]" value="Alpha" {{ $currentStatus == 'Alpha' ? 'checked' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5">Simpan Data Absensi</button>
            </div>
        </form>
    </div>
</div>
@endsection