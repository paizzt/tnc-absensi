<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi - {{ $school->name ?? 'Sekolah' }}</title>
    <style>
        @page {
            size: landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
            padding: 15px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #444;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f3f3f3;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .sign-table {
            width: 100%;
            border: none;
            margin-top: 45px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .sign-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #2563EB; color: #fff; border: none; cursor: pointer; border-radius: 4px; font-weight: bold;">Cetak</button>
    </div>

    <div class="header">
        <h2>LAPORAN REKAPITULASI ABSENSI SISWA</h2>
        <h2>{{ $school->name ?? '' }}</h2>
        <p>Periode: {{ date('d/m/Y', strtotime($request->start_date)) }} s.d. {{ date('d/m/Y', strtotime($request->end_date)) }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">no</th>
                <th rowspan="2" style="width: 85px;">NIS</th>
                <th rowspan="2">Nama</th>
                <th rowspan="2" style="width: 75px;">kelas</th>
                <th rowspan="2" style="width: 80px;">absen check in</th>
                <th colspan="9">status kehadiran permatkul (perjam)</th>
                <th rowspan="2" style="width: 80px;">absen check out</th>
                <th rowspan="2" style="width: 75px;">tanggal</th>
            </tr>
            <tr>
                <th style="width: 25px;">1</th>
                <th style="width: 25px;">2</th>
                <th style="width: 25px;">3</th>
                <th style="width: 25px;">4</th>
                <th style="width: 25px;">5</th>
                <th style="width: 25px;">6</th>
                <th style="width: 25px;">7</th>
                <th style="width: 25px;">8</th>
                <th style="width: 25px;">9</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $row['nis'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td class="text-center">{{ $row['classroom'] }}</td>
                <td class="text-center">{{ $row['scan_in'] }}</td>
                @for($p = 1; $p <= 9; $p++)
                    <td class="text-center">{{ $row['periods'][$p] ?? '-' }}</td>
                @endfor
                <td class="text-center">{{ $row['scan_out'] }}</td>
                <td class="text-center">{{ $row['date'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="16" class="text-center" style="padding: 20px;">Tidak ada data absensi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign-table">
        <tr>
            <td style="width: 35%; text-align: center;">
                <p style="margin: 0 0 60px 0; font-size: 12px;">kepala sekolah</p>
                <p style="margin: 0; font-size: 12px; font-weight: bold; text-decoration: underline;">{{ $kepsekName }}</p>
            </td>
            <td style="width: 30%;"></td>
            <td style="width: 35%; text-align: center;">
                <p style="margin: 0 0 60px 0; font-size: 12px;">di eksport oleh</p>
                <p style="margin: 0; font-size: 12px; font-weight: bold; text-decoration: underline;">{{ $exporterName }}</p>
            </td>
        </tr>
    </table>
</body>
</html>