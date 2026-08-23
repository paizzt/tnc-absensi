<!DOCTYPE html>
<html>
<head>
    <title>Pemberitahuan Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #004085;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin-top: 0;
        }
        .content {
            padding: 20px;
            background-color: white;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="header">Laporan Absensi {{ $type }}</h2>
        <div class="content">
            <p>Yth. Orang Tua / Wali Murid,</p>
            <p>Kami memberitahukan bahwa ananda:</p>
            
            <table style="width: 100%; margin-bottom: 20px;">
                <tr>
                    <td style="width: 30%;"><strong>Nama</strong></td>
                    <td>: {{ $studentName }}</td>
                </tr>
                <tr>
                    <td><strong>Waktu Scan</strong></td>
                    <td>: {{ $time }}</td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>: <strong>{{ $status }}</strong></td>
                </tr>
            </table>

            @if($messageText)
                <p>{{ $messageText }}</p>
            @endif

            <p>Terima kasih atas perhatian Anda.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Sistem Presensi Sekolah. Email ini dibuat otomatis oleh sistem.
        </div>
    </div>
</body>
</html>
