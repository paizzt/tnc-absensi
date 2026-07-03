@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<style>
    /* Styling khusus matriks roster view */
    .roster-table-wrapper {
        overflow-x: auto;
    }
    .roster-table {
        width: 100%;
        min-width: 1200px;
        border-collapse: collapse;
        background-color: #fff;
    }
    .roster-table th {
        background-color: #145f4e; /* Hijau gelap */
        color: #ffffff;
        text-align: center;
        padding: 12px 8px;
        font-weight: 500;
        font-size: 0.85rem;
        border: 1px solid #104a3d;
    }
    .roster-table th .time-slot {
        font-size: 0.7rem;
        color: #a7f3d0;
        display: block;
        margin-top: 4px;
    }
    .roster-table td {
        border: 1px solid #e5e7eb;
        padding: 10px;
        vertical-align: top;
        position: relative;
    }
    .roster-table td.day-cell {
        background-color: #ffffff;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        color: #111827;
        width: 80px;
    }
    .roster-table td:nth-child(even):not(.day-cell) {
        background-color: #f0fdf4;
    }
    .break-column {
        background-color: #fef3c7 !important;
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
    
    /* Styling Accordion */
    .accordion-button:not(.collapsed) {
        color: #145f4e;
        background-color: #ecfdf5;
        box-shadow: inset 0 calc(-1 * var(--bs-accordion-border-width)) 0 #a7f3d0;
    }
    
    /* Tombol Hapus Sel (Cell) */
    .btn-delete-cell {
        position: absolute;
        bottom: 4px;
        right: 4px;
        font-size: 0.65rem;
        padding: 2px 5px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .roster-table td:hover .btn-delete-cell {
        opacity: 1;
    }
</style>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Daftar Roster Matriks</h4>
            <p class="text-neutral small mb-0">Klik pada nama kelas untuk melihat dan mengelola daftar mapel secara visual.</p>
        </div>
        <a href="{{ route('admin.schedules.create', ['school_id' => $selectedSchoolId ?? '']) }}" class="btn btn-primary btn-sm px-3 fw-medium shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Susun Roster Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.schedules.index') }}" method="GET" class="d-flex align-items-center">
                <label class="fw-semibold text-primary me-3 mb-0" style="white-space: nowrap;">
                    <i class="bi bi-buildings me-1"></i> Filter Sekolah:
                </label>
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

    @if($classrooms->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="text-neutral mb-3"><i class="bi bi-calendar-x fs-1 text-opacity-50"></i></div>
            <h6 class="fw-bold text-dark">Belum Ada Roster Pelajaran</h6>
            <p class="small text-neutral mb-0">Klik tombol "Susun Roster Baru" di atas untuk mengisi matriks jadwal kelas.</p>
        </div>
    @else
        <div class="accordion" id="accordionSchedules">
            @foreach($classrooms as $class)
                <div class="accordion-item border-0 shadow-sm mb-3 rounded-4 overflow-hidden">
                    <h2 class="accordion-header" id="heading{{ $class->id }}">
                        <button class="accordion-button collapsed fw-bold fs-6 border-bottom" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $class->id }}" aria-expanded="false" aria-controls="collapse{{ $class->id }}">
                            <i class="bi bi-mortarboard"></i> Roster Kelas: {{ $class->level }} - {{ $class->name }}
                        </button>
                    </h2>
                    <div id="collapse{{ $class->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $class->id }}" data-bs-parent="#accordionSchedules">
                        <div class="accordion-body p-0">
                            
                            <div class="d-flex justify-content-end bg-light p-2 border-bottom">
                                <a href="{{ route('admin.schedules.edit', $class->id) }}" class="btn btn-sm btn-warning text-dark fw-bold me-2 shadow-sm">
                                    <i class="bi bi-pencil-square"></i> Edit Roster Ini
                                </a>
                                <form action="{{ route('admin.schedules.destroy_class', $class->id) }}" method="POST" class="sweet-delete-form" data-title="Reset Semua Jadwal?" data-text="Seluruh roster di kelas ini akan dihapus secara permanen!">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm">
                                        <i class="bi bi-trash3-fill"></i> Reset (Hapus Semua)
                                    </button>
                                </form>
                            </div>

                            <div class="roster-table-wrapper">
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
                                                        @php
                                                            $sched = isset($schedules[$class->id]) ? $schedules[$class->id]->filter(function($s) use ($day, $slot) {
                                                                return $s->day_of_week == $day && date('H:i', strtotime($s->start_time)) == $slot['start'];
                                                            })->first() : null;
                                                        @endphp

                                                        @if($sched)
                                                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $sched->subject->name }}</div>
                                                            <div class="text-neutral mt-1" style="font-size: 0.75rem;"><i class="bi bi-person-fill text-primary"></i> {{ $sched->teacher->name }}</div>
                                                            
                                                            <form action="{{ route('admin.schedules.destroy', $sched->id) }}" method="POST" class="sweet-delete-form" data-title="Hapus Mapel Ini?" data-text="Mapel {{ $sched->subject->name }} akan dihapus dari jam ini.">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-cell" title="Hapus Mapel Ini">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <div class="text-center text-muted" style="font-size: 0.75rem; opacity: 0.4;">- Kosong -</div>
                                                        @endif
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Menangkap event submit pada form dengan class 'sweet-delete-form'
        $('.sweet-delete-form').on('submit', function(e) {
            e.preventDefault(); // Hentikan proses form bawaan browser
            let form = this;
            
            // Ambil pesan dinamis dari atribut data- HTML
            let titleText = $(this).data('title') || 'Apakah Anda Yakin?';
            let messageText = $(this).data('text') || 'Data ini akan dihapus!';
            
            // Tampilkan pop-up SweetAlert2
            Swal.fire({
                title: titleText,
                text: messageText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                // Jika user menekan tombol 'Ya, Hapus'
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection