<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak ID Card - {{ $student->name }}</title>
    <style>
        body { margin: 0; padding: 20px; display: flex; justify-content: center; background: #e5e7eb; font-family: Arial, sans-serif; }
        
        /* Desain Kartu Sesuai Sketsa Anda */
        .id-card {
            width: 240px;
            height: 380px;
            border: 3px solid #000;
            border-radius: 12px;
            background: #fff;
            position: relative;
            box-sizing: border-box;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .logo-box {
            border: 2px solid #000;
            border-radius: 8px;
            padding: 5px 15px;
            margin-bottom: 20px;
        }

        .logo-box img {
            max-height: 35px;
            display: block;
        }

        .qr-box {
            border: 2px solid #000;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: auto; /* Mendorong teks ke bawah */
        }

        .text-box {
            text-align: center;
            width: 100%;
        }

        .student-name {
            font-size: 16px;
            font-weight: 900;
            color: #000;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .student-nis {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .no-print { margin-bottom: 20px; text-align: center; width: 100%; position: absolute; top: 10px; }
        .btn-print { padding: 10px 20px; background: #2563EB; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        
        @media print {
            body { background: #fff; padding: 0; align-items: flex-start; justify-content: flex-start; }
            .id-card { box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Kartu Ini</button>
    </div>

    <div class="id-card">
        <div class="logo-box">
            <img src="{{ asset('images/logo.png') }}" alt="LOGO">
        </div>
        
        <div class="qr-box">
            {!! QrCode::size(140)->margin(0)->generate($student->qr_code_string) !!}
        </div>
        
        <div class="text-box">
            <div class="student-name">{{ $student->name }}</div>
            <div class="student-nis">{{ $student->nis }}</div>
        </div>
    </div>

</body>
</html>