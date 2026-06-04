<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mengekstrak Kartu ZIP...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; }
        .hidden-cards-container { 
            position: absolute; 
            top: -9999px; 
            left: -9999px; 
            visibility: hidden; 
        }
        
        /* Desain Kartu (Sama dengan satuan) */
        .id-card {
            width: 240px;
            height: 380px;
            border: 3px solid #000;
            border-radius: 12px;
            background: #fff;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin-bottom: 20px;
        }
        .logo-box { border: 2px solid #000; border-radius: 8px; padding: 5px 15px; margin-bottom: 20px; }
        .logo-box img { max-height: 35px; }
        .qr-box { border: 2px solid #000; border-radius: 8px; padding: 15px; margin-bottom: auto; background: #fff;}
        .text-box { text-align: center; width: 100%; }
        .student-name { font-size: 16px; font-weight: 900; color: #000; margin-bottom: 5px; text-transform: uppercase; }
        .student-nis { font-size: 14px; font-weight: bold; color: #000; }
    </style>
</head>
<body>

    <div class="container d-flex flex-column align-items-center justify-content-center min-vh-100">
        <div class="card border-0 shadow-lg p-5 text-center rounded-4" style="max-width: 500px;">
            <div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
            <h4 class="fw-bold text-dark">Menyusun File ZIP...</h4>
            <p class="text-muted mb-4">Sistem sedang merender <strong class="text-primary">{{ $students->count() }}</strong> kartu ID. Mohon jangan tutup atau muat ulang halaman ini.</p>
            
            <div class="progress mb-3" style="height: 20px;">
                <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;">0%</div>
            </div>
            
            <a href="{{ route('admin.students.index', ['school_id' => request('school_id')]) }}" class="btn btn-light border mt-3 d-none" id="btn-back">Kembali ke Daftar Siswa</a>
        </div>
    </div>

    <div class="hidden-cards-container" id="cards-container">
        @foreach($students as $student)
            <div class="id-card" data-nis="{{ $student->nis }}" id="card-{{ $student->id }}">
                <div class="logo-box">
                    <img src="{{ asset('images/logo.png') }}" alt="LOGO">
                </div>
                <div class="qr-box">
                    {!! QrCode::size(140)->margin(0)->format('svg')->generate($student->qr_code_string) !!}
                </div>
                <div class="text-box">
                    <div class="student-name">{{ $student->name }}</div>
                    <div class="student-nis">{{ $student->nis }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            const zip = new JSZip();
            const cards = document.querySelectorAll('.id-card');
            const totalCards = cards.length;
            const progressBar = document.getElementById('progress-bar');
            
            // Buat folder di dalam ZIP
            const folder = zip.folder("Kartu_ID_Siswa");

            for (let i = 0; i < totalCards; i++) {
                let card = cards[i];
                let nis = card.getAttribute('data-nis');
                
                // Render HTML menjadi Gambar Canvas (Skala 3 agar High-Resolution/HD)
                let canvas = await html2canvas(card, { 
                    scale: 3, 
                    backgroundColor: "#ffffff",
                    logging: false
                });
                
                // Konversi ke format Base64 (PNG)
                let imgData = canvas.toDataURL("image/png").split('base64,')[1];
                
                // Masukkan file gambar ke dalam Folder ZIP
                folder.file("Kartu_" + nis + ".png", imgData, {base64: true});

                // Update Progress Bar
                let percent = Math.round(((i + 1) / totalCards) * 100);
                progressBar.style.width = percent + '%';
                progressBar.innerText = percent + '%';
            }

            // Generate dan Download File ZIP
            zip.generateAsync({type:"blob"}).then(function(content) {
                saveAs(content, "Kartu_Barcode_Siswa.zip");
                
                // Tampilkan tombol kembali setelah selesai
                document.querySelector('h4').innerText = "ZIP Berhasil Diunduh!";
                document.querySelector('h4').classList.replace('text-dark', 'text-success');
                document.querySelector('.spinner-border').classList.add('d-none');
                document.getElementById('btn-back').classList.remove('d-none');
            });
        });
    </script>
</body>
</html>