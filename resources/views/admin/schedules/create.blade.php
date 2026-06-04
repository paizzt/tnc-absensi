@extends('layouts.app')

@section('title', 'Penyusunan Roster')

@section('content')
<style>
    /* Styling khusus mengikuti referensi gambar */
    .roster-table-wrapper {
        overflow-x: auto;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .roster-table {
        width: 100%;
        min-width: 1200px; /* Agar tidak terlalu menyusut */
        border-collapse: collapse;
        background-color: #fff;
    }
    .roster-table th {
        background-color: #145f4e; /* Hijau gelap khas roster */
        color: #ffffff;
        text-align: center;
        padding: 12px 8px;
        font-weight: 500;
        font-size: 0.85rem;
        border: 1px solid #104a3d;
    }
    .roster-table th .time-slot {
        font-size: 0.7rem;
        color: #a7f3d0; /* Hijau muda */
        display: block;
        margin-top: 4px;
    }
    .roster-table td {
        border: 1px solid #e5e7eb;
        padding: 8px;
        vertical-align: top;
    }
    .roster-table td.day-cell {
        background-color: #ffffff;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        color: #111827;
        width: 80px;
    }
    /* Warna selongsong kolom bergantian */
    .roster-table td:nth-child(even):not(.day-cell) {
        background-color: #f0fdf4; /* Hijau sangat muda */
    }
    
    .break-column {
        background-color: #fef3c7 !important; /* Kuning soft untuk istirahat */
        color: #b45309;
        font-weight: bold;
        text-align: center;
        vertical-align: middle !important;
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transform: rotate(180deg);
        letter-spacing: 2px;
        width: 50px;
    }

    /* Style untuk form select dalam grid */
    .cell-select {
        width: 100%;
        border: 1px solid transparent;
        background-color: transparent;
        font-size: 0.75rem;
        padding: 4px;
        border-radius: 4px;
        margin-bottom: 4px;
        color: #374151;
        cursor: pointer;
    }
    .cell-select:hover, .cell-select:focus {
        border-color: #10b981;
        background-color: #fff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
    }
    .select-mapel { font-weight: 600; }
    .select-guru { color: #6b7280; }
</style>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Tabel Roster Mingguan</h4>
            <p class="text-neutral small mb-0">Isi mapel dan guru pada kotak yang tersedia. Kosongkan jika tidak ada pelajaran.</p>
        </div>
        <a href="{{ route('admin.schedules.index', ['school_id' => $schoolId]) }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger" style="background-color: #fef2f2; border-color: #fecaca; color: #DC2626;">
            <strong>Gagal Menyimpan!</strong> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.schedules.store') }}" method="POST">
        @csrf
        
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="row align-items-end">
                    
                    @role('Super Admin')
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label class="form-label text-primary small fw-semibold">🏢 Penempatan Sekolah (Mode Super Admin)</label>
                        <select class="form-select border-primary fw-medium" name="school_id" onchange="window.location.href='{{ route('admin.schedules.create') }}?school_id=' + this.value" required>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                        <input type="hidden" name="school_id" value="{{ $schoolId }}">
                    @endrole
                    
                    <div class="col-md-5">
                        <label class="form-label text-dark small fw-bold">🎓 Pilih Kelas yang Akan Disusun <span class="text-danger">*</span></label>
                        <select class="form-select border-dark" name="classroom_id" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $class)
                                <option value="{{ $class->id }}" {{ old('classroom_id') == $class->id ? 'selected' : '' }}>{{ $class->level }} - {{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2 text-end mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">💾 Simpan Roster</button>
                    </div>
                </div>
                @if($classrooms->isEmpty())
                    <div class="form-text text-danger small mt-2">Belum ada kelas. Buat di Master Kelas terlebih dahulu.</div>
                @endif
            </div>
        </div>

        <div class="roster-table-wrapper mb-5 bg-white">
            <table class="roster-table">
                <thead>
                    <tr>
                        <th width="80px">Hari</th>
                        @foreach($timeSlots as $slot)
                            @if($slot['is_break'])
                                <th style="background-color: #0f4a3d;">{{ $slot['name'] }}<span class="time-slot">{{ $slot['time'] }}</span></th>
                            @else
                                <th>{{ $slot['name'] }}<span class="time-slot">{{ $slot['time'] }}</span></th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($days as $dayIndex => $day)
                    <tr>
                        <td class="day-cell">{{ $day }}</td>
                        
                        @foreach($timeSlots as $slotIndex => $slot)
                            @if($slot['is_break'])
                                @if($dayIndex == 0)
                                    <td rowspan="{{ count($days) }}" class="break-column">ISTIRAHAT</td>
                                @endif
                            @else
                                <td>
                                    <input type="hidden" name="roster[{{ $day }}][{{ $slotIndex }}][start_time]" value="{{ $slot['start'] }}">
                                    <input type="hidden" name="roster[{{ $day }}][{{ $slotIndex }}][end_time]" value="{{ $slot['end'] }}">
                                    
                                    <select class="cell-select select-mapel" name="roster[{{ $day }}][{{ $slotIndex }}][subject_id]">
                                        <option value="">- Mapel -</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old("roster.{$day}.{$slotIndex}.subject_id") == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <select class="cell-select select-guru" name="roster[{{ $day }}][{{ $slotIndex }}][teacher_id]">
                                        <option value="">- Guru -</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old("roster.{$day}.{$slotIndex}.teacher_id") == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection