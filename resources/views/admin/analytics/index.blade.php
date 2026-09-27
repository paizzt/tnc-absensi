@extends('layouts.app')

@section('title', 'Analitik Data')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Analitik Data</h4>
            <p class="text-neutral small mb-0">Visualisasi data absensi dan akademik sekolah.</p>
        </div>
    </div>

    @role('Super Admin')
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.analytics.index') }}" method="GET" class="d-flex align-items-center">
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

    <div class="row g-4 mb-4">
        <!-- Kehadiran 7 Hari Terakhir -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-dark"><i class="bi bi-graph-up text-primary me-2"></i> Tren Kehadiran (7 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px; width: 100%;">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Kehadiran Hari Ini -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-dark"><i class="bi bi-pie-chart-fill text-success me-2"></i> Kehadiran Hari Ini</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="height: 250px; width: 100%;">
                        <canvas id="todayStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jumlah Siswa per Kelas -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-dark"><i class="bi bi-bar-chart-fill text-warning me-2"></i> Distribusi Siswa per Kelas</h6>
                </div>
                <div class="card-body">
                    <div style="height: 350px; width: 100%;">
                        <canvas id="studentClassChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Tren Kehadiran 7 Hari Terakhir (Line Chart)
        const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($last7Days),
                datasets: [
                    {
                        label: 'Hadir (Tepat Waktu)',
                        data: @json($onTimeData),
                        borderColor: '#10b981', // green
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Terlambat',
                        data: @json($lateData),
                        borderColor: '#f59e0b', // yellow/orange
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // 2. Status Kehadiran Hari Ini (Doughnut Chart)
        const todayCtx = document.getElementById('todayStatusChart').getContext('2d');
        const statusLabels = @json($statusLabels);
        const statusCounts = @json($statusCounts);
        
        // Define colors based on status if possible, or fallback array
        const bgColors = statusLabels.map(status => {
            if(status.toLowerCase() === 'hadir') return '#10b981';
            if(status.toLowerCase() === 'terlambat') return '#f59e0b';
            if(status.toLowerCase() === 'izin') return '#3b82f6';
            if(status.toLowerCase() === 'sakit') return '#8b5cf6';
            if(status.toLowerCase() === 'alpa') return '#ef4444';
            return '#6b7280';
        });

        new Chart(todayCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels.length ? statusLabels : ['Belum Ada Data'],
                datasets: [{
                    data: statusCounts.length ? statusCounts : [1],
                    backgroundColor: statusCounts.length ? bgColors : ['#e5e7eb'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Distribusi Siswa per Kelas (Bar Chart)
        const classCtx = document.getElementById('studentClassChart').getContext('2d');
        new Chart(classCtx, {
            type: 'bar',
            data: {
                labels: @json($classLabels),
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: @json($studentCounts),
                    backgroundColor: '#3b82f6', // blue
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 5 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection
