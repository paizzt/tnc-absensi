<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran Mingguan</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f6f9;
            padding: 30px 0;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            max-width: 650px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            padding: 25px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 6px 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 25px;
        }
        .student-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 22px;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-table td {
            padding: 4px 6px;
            font-size: 13px;
            vertical-align: top;
        }
        .student-table td.label {
            width: 30%;
            color: #64748b;
            font-weight: 600;
        }
        .student-table td.val {
            color: #0f172a;
            font-weight: 600;
        }
        .stat-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 22px;
        }
        .stat-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 6px;
            text-align: center;
        }
        .stat-num {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 2px 0;
        }
        .stat-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .stat-green { color: #16a34a; }
        .stat-yellow { color: #d97706; }
        .stat-blue { color: #2563eb; }
        .stat-red { color: #dc2626; }
        .stat-purple { color: #7c3aed; }
        
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 10px 0;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .log-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            text-align: left;
            padding: 9px 8px;
            border: 1px solid #cbd5e1;
        }
        .log-table td {
            padding: 8px;
            border: 1px solid #cbd5e1;
            color: #334155;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
        }
        .badge-hadir { background-color: #dcfce7; color: #15803d; }
        .badge-terlambat { background-color: #fef3c7; color: #b45309; }
        .badge-izin { background-color: #dbeafe; color: #1e40af; }
        .badge-sakit { background-color: #e0e7ff; color: #4338ca; }
        .badge-alpha { background-color: #fee2e2; color: #b91c1c; }
        .badge-libur { background-color: #f1f5f9; color: #64748b; }

        .note-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 14px;
            font-size: 12px;
            color: #1e40af;
            line-height: 1.5;
            margin-top: 15px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            padding: 18px 25px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main">
            <!-- Header -->
            <div class="header">
                <h1>{{ $student->school ? $student->school->name : 'SISTEM ABSENSI SEKOLAH' }}</h1>
                <p>Laporan Rekapitulasi Kehadiran Mingguan Siswa</p>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Data Siswa -->
                <div class="student-card">
                    <table class="student-table">
                        <tr>
                            <td class="label">Nama Siswa</td>
                            <td class="val">: {{ $student->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">NIS</td>
                            <td class="val">: {{ $student->nis ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelas</td>
                            <td class="val">: {{ $student->classroom ? $student->classroom->name : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Periode Laporan</td>
                            <td class="val">: {{ date('d/m/Y', strtotime($startDate)) }} s.d. {{ date('d/m/Y', strtotime($endDate)) }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Summary KPI Cards -->
                <table class="stat-grid">
                    <tr>
                        <td class="stat-card" style="width: 20%;">
                            <p class="stat-num stat-green">{{ $summary['total_hadir'] ?? 0 }}</p>
                            <p class="stat-title">Hadir</p>
                        </td>
                        <td class="stat-card" style="width: 20%;">
                            <p class="stat-num stat-yellow">{{ $summary['total_terlambat'] ?? 0 }}</p>
                            <p class="stat-title">Terlambat</p>
                        </td>
                        <td class="stat-card" style="width: 20%;">
                            <p class="stat-num stat-blue">{{ ($summary['total_sakit'] ?? 0) + ($summary['total_izin'] ?? 0) }}</p>
                            <p class="stat-title">Sakit/Izin</p>
                        </td>
                        <td class="stat-card" style="width: 20%;">
                            <p class="stat-num stat-red">{{ $summary['total_alpha'] ?? 0 }}</p>
                            <p class="stat-title">Alpha</p>
                        </td>
                        <td class="stat-card" style="width: 20%;">
                            <p class="stat-num stat-purple">{{ $summary['persentase'] ?? 0 }}%</p>
                            <p class="stat-title">Kehadiran</p>
                        </td>
                    </tr>
                </table>

                <!-- Detail Harian -->
                <h3 class="section-title">Rincian Kehadiran Harian</h3>
                <table class="log-table">
                    <thead>
                        <tr>
                            <th style="width: 24%;">Hari & Tanggal</th>
                            <th style="width: 18%;" class="text-center">Jam Masuk</th>
                            <th style="width: 18%;" class="text-center">Jam Pulang</th>
                            <th style="width: 22%;">Kehadiran Mapel</th>
                            <th style="width: 18%;" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyRecords as $record)
                        <tr>
                            <td>
                                <strong>{{ $record['day_name'] }}</strong><br>
                                <span style="font-size: 11px; color: #64748b;">{{ $record['formatted_date'] }}</span>
                            </td>
                            <td class="text-center">{{ $record['scan_in'] ?? '-' }}</td>
                            <td class="text-center">{{ $record['scan_out'] ?? '-' }}</td>
                            <td>{{ $record['class_summary'] ?? '-' }}</td>
                            <td class="text-center">
                                @php
                                    $st = strtolower($record['status']);
                                    $badgeClass = 'badge-libur';
                                    if (str_contains($st, 'hadir')) $badgeClass = 'badge-hadir';
                                    elseif (str_contains($st, 'terlambat')) $badgeClass = 'badge-terlambat';
                                    elseif (str_contains($st, 'izin')) $badgeClass = 'badge-izin';
                                    elseif (str_contains($st, 'sakit')) $badgeClass = 'badge-sakit';
                                    elseif (str_contains($st, 'alpha')) $badgeClass = 'badge-alpha';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $record['status'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 15px; color: #94a3b8;">Tidak ada data kehadiran pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Catatan untuk Wali Murid -->
                <div class="note-box">
                    <strong>Pemberitahuan untuk Orang Tua / Wali Murid:</strong><br>
                    Laporan ini dibuat dan dikirimkan secara otomatis setiap minggu oleh sistem presensi digital sekolah guna membantu Bapak/Ibu memantau kedisiplinan dan absensi ananda selama proses kegiatan belajar mengajar. Jika terdapat ketidaksesuaian data, silakan menghubungi pihak sekolah atau wali kelas.
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p style="margin: 0 0 4px 0;"><strong>{{ $student->school ? $student->school->name : 'Sistem Presensi Sekolah' }}</strong></p>
                <p style="margin: 0;">Email ini dikirim secara otomatis oleh sistem presensi. Mohon untuk tidak membalas langsung ke email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
