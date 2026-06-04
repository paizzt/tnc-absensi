@extends('layouts.app')

@section('title', 'Edit Roster')

@section('content')
<style>
    .roster-table-wrapper { overflow-x: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .roster-table { width: 100%; min-width: 1200px; border-collapse: collapse; background-color: #fff; }
    .roster-table th { background-color: #145f4e; color: #fff; text-align: center; padding: 12px 8px; border: 1px solid #104a3d; }
    .roster-table th .time-slot { font-size: 0.7rem; color: #a7f3d0; display: block; margin-top: 4px; }
    .roster-table td { border: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
    .roster-table td.day-cell { background-color: #fff; font-weight: 600; text-align: center; vertical-align: middle; width: 80px; }
    .roster-table td:nth-child(even):not(.day-cell) { background-color: #f0fdf4; }
    .break-column { background-color: #fef3c7 !important; color: #b45309; font-weight: bold; text-align: center; vertical-align: middle !important; writing-mode: vertical-rl; transform: rotate(180deg); width: 50px; }
    .cell-select { width: 100%; border: 1px solid #d1d5db; background-color: #fff; font-size: 0.75rem; padding: 4px; border-radius: 4px; margin-bottom: 4px; cursor: pointer; }
    .cell-select:focus { border-color: #10b981; outline: none; box-shadow: 0 0 0 2px rgba(16,185,129,0.1); }
    .select-mapel { font-weight: 600; }
    .has-data { border-color: #10b981; background-color: #ecfdf5; }
</style>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Ubah Roster Kelas: {{ $classroom->name }}</h4>
            <p class="text-neutral small mb-0">Ubah mapel/guru pada kotak yang diinginkan.</p>
        </div>
        <a href="{{ route('admin.schedules.index', ['school_id' => $schoolId]) }}" class="btn btn-light btn-sm px-3 border">Batal</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger"><strong>BENTROK!</strong> {{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.schedules.update', $classroom->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="text-end mb-3">
            <button type="submit" class="btn btn-warning fw-bold text-dark px-4 shadow-sm"><i class="bi bi-save-fill me-1"></i> Update Matriks</button>
        </div>

        <div class="roster-table-wrapper mb-5">
            <table class="roster-table">
                <thead>
                    <tr>
                        <th width="80px">Hari</th>
                        @foreach($timeSlots as $slot)
                            <th {!! $slot['is_break'] ? 'style="background-color: #0f4a3d;"' : '' !!}>
                                {{ $slot['name'] }}<span class="time-slot">{{ $slot['time'] }}</span>
                            </th>
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
                                @php
                                    // Cari apakah ada data lama di kotak ini
                                    $oldSched = $existingSchedules->filter(function($s) use ($day, $slot) {
                                        return $s->day_of_week == $day && date('H:i', strtotime($s->start_time)) == $slot['start'];
                                    })->first();
                                    $hasDataClass = $oldSched ? 'has-data' : '';
                                @endphp
                                <td>
                                    <input type="hidden" name="roster[{{ $day }}][{{ $slotIndex }}][start_time]" value="{{ $slot['start'] }}">
                                    <input type="hidden" name="roster[{{ $day }}][{{ $slotIndex }}][end_time]" value="{{ $slot['end'] }}">
                                    
                                    <select class="cell-select select-mapel {{ $hasDataClass }}" name="roster[{{ $day }}][{{ $slotIndex }}][subject_id]">
                                        <option value="">- Kosong -</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ ($oldSched && $oldSched->subject_id == $subject->id) ? 'selected' : '' }}>{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <select class="cell-select {{ $hasDataClass }}" name="roster[{{ $day }}][{{ $slotIndex }}][teacher_id]">
                                        <option value="">- Kosong -</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ ($oldSched && $oldSched->teacher_id == $teacher->id) ? 'selected' : '' }}>{{ $teacher->name }}</option>
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