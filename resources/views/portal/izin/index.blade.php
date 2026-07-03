<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Perizinan Siswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .portal-card { max-width: 450px; width: 100%; padding: 2rem; background: #fff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .nav-pills .nav-link { color: #6b7280; font-weight: 600; border-radius: 8px; }
        .nav-pills .nav-link.active { background-color: #2563EB; color: #fff; }
        #reader { width: 100%; border-radius: 8px; overflow: hidden; border: none !important; }
        #reader video { border-radius: 8px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="portal-card">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-height: 60px; margin-bottom: 1rem;">
            <h5 class="fw-bold text-dark mb-1">Portal Kehadiran Siswa</h5>
            <p class="text-muted small">Layanan permohonan Sakit & Izin secara daring.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success small py-2"><i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger small py-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}</div>
        @endif

        <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-scan-tab" data-bs-toggle="pill" data-bs-target="#pills-scan" type="button" role="tab">
                    <i class="bi bi-qr-code-scan me-1"></i> Scan Kartu
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button" role="tab">
                    <i class="bi bi-keyboard me-1"></i> Ketik NIS
                </button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade show active" id="pills-scan" role="tabpanel">
                <div class="text-center mb-3">
                    <p class="small text-muted mb-2">Arahkan kamera ke QR Code pada ID Card Siswa.</p>
                    <div id="reader"></div>
                    <button id="start-scan-btn" class="btn btn-outline-primary w-100 fw-bold mt-3">
                        <i class="bi bi-camera me-1"></i> Aktifkan Kamera Pemindai
                    </button>
                    <button id="stop-scan-btn" class="btn btn-outline-danger w-100 fw-bold mt-3 d-none">
                        <i class="bi bi-stop-circle me-1"></i> Hentikan Kamera
                    </button>
                </div>
                
                <form action="{{ route('portal.izin.search') }}" method="POST" id="scan-form">
                    @csrf
                    <input type="hidden" name="identifier" id="scan-identifier">
                </form>
            </div>

            <div class="tab-pane fade" id="pills-manual" role="tabpanel">
                <form action="{{ route('portal.izin.search') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-dark fw-semibold small">Nomor Induk Siswa (NIS)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-vcard text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" name="identifier" required placeholder="Contoh: 101234">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                        Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const html5QrCode = new Html5Qrcode("reader");
            const startBtn = document.getElementById('start-scan-btn');
            const stopBtn = document.getElementById('stop-scan-btn');
            const scanForm = document.getElementById('scan-form');
            const scanIdentifier = document.getElementById('scan-identifier');

            function playBeep() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);

                    oscillator.type = 'sine';
                    oscillator.frequency.value = 800;
                    gainNode.gain.setValueAtTime(1, audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.1);
                    oscillator.start(audioCtx.currentTime);
                    oscillator.stop(audioCtx.currentTime + 0.1);
                } catch (e) {
                    console.log('Audio error:', e);
                }
            }

            function onScanSuccess(decodedText, decodedResult) {
                playBeep();
                // Hentikan pemindaian jika berhasil
                html5QrCode.stop().then((ignore) => {
                    // Masukkan kode QR ke dalam form tersembunyi dan otomatis kirim
                    scanIdentifier.value = decodedText;
                    setTimeout(() => {
                        scanForm.submit();
                    }, 200); // Jeda agar bunyi selesai
                }).catch((err) => {
                    console.log(err);
                });
            }

            function onScanFailure(error) {
                // Diabaikan, scanner akan terus mencoba membaca
            }

            startBtn.addEventListener('click', function() {
                startBtn.classList.add('d-none');
                stopBtn.classList.remove('d-none');
                
                // Gunakan kamera belakang (environment) secara default
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    onScanFailure
                ).catch((err) => {
                    alert("Kamera tidak dapat diakses. Pastikan Anda memberikan izin akses kamera.");
                    startBtn.classList.remove('d-none');
                    stopBtn.classList.add('d-none');
                });
            });

            stopBtn.addEventListener('click', function() {
                html5QrCode.stop().then((ignore) => {
                    startBtn.classList.remove('d-none');
                    stopBtn.classList.add('d-none');
                }).catch((err) => {
                    console.log(err);
                });
            });

            // Hentikan kamera jika berpindah ke Tab Ketik NIS
            const manualTab = document.getElementById('pills-manual-tab');
            manualTab.addEventListener('shown.bs.tab', function () {
                if (html5QrCode.isScanning) {
                    html5QrCode.stop().then((ignore) => {
                        startBtn.classList.remove('d-none');
                        stopBtn.classList.add('d-none');
                    });
                }
            });
        });
    </script>
</body>
</html>