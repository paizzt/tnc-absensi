<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi - {{ $school->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 15px; }
        .header h2 { margin: 0 0 5px 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 0; font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px 10px; text-align: left; }
        th { background-color: #f3f3f3; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; text-align: right; }
        .sign-area { display: inline-block; text-align: center; margin-right: 50px; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563EB; color: #fff; border: none; cursor: pointer;">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h2>LAPORAN REKAPITULASI ABSENSI SISWA</h2>
        <h2>{{ $school->name }}</h2>
        <p>Periode: {{ date('d/m/Y', strtotime($request->start_date)) }} s.d. {{ date('d/m/Y', strtotime($request->end_date)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%">Status</th>
                <th width="15%">Jam Masuk</th>
                <th width="15%">Jam Pulang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($row->date)) }}</td>
                <td>{{ $row->student->name ?? '-' }}</td>
                <td class="text-center">{{ $row->student->classroom->name ?? '-' }}</td>
                <td class="text-center">{{ strtoupper($row->status) }}</td>
                <td class="text-center">{{ $row->scan_in ?? '-' }}</td>
                <td class="text-center">{{ $row->scan_out ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data absensi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="sign-area">
            <p>Makassar, {{ date('d F Y') }}</p>
            <p style="margin-bottom: 70px;">Mengetahui,<br>Kepala Sekolah</p>
            <p>_______________________</p>
        </div>
    </div>
</body>
</html>