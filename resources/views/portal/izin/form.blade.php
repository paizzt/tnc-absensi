<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Izin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; padding: 20px 0; }
        .form-container { max-width: 500px; margin: auto; background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        #video-container { background: #000; border-radius: 8px; overflow: hidden; position: relative; margin-bottom: 10px; }
        #video, #photo-preview { width: 100%; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="d-flex align-items-center border-bottom pb-3 mb-4">
                <a href="{{ route('portal.izin.index') }}" class="btn btn-sm btn-light border me-3"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Formulir Kehadiran</h5>
                    <span class="text-muted small">Siswa: {{ $student->name }} ({{ $student->classroom->name }})</span>
                </div>
            </div>

            <form action="{{ route('portal.izin.submit', $student->id) }}" method="POST" enctype="multipart/form-data" id="izinForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Keterangan Kehadiran</label>
                    <select class="form-select" name="type" required>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin (Kepentingan Keluarga)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Alasan / Pesan Singkat</label>
                    <textarea class="form-control" name="reason" rows="2" required placeholder="Contoh: Demam sejak tadi malam..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Foto Bukti Keadaan Saat Ini (Wajib)</label>
                    
                    <div id="video-container">
                        <video id="video" autoplay playsinline></video>
                        <img id="photo-preview" class="d-none" />
                    </div>
                    
                    <button type="button" id="snap-btn" class="btn btn-secondary w-100 mb-2"><i class="bi bi-camera"></i> Ambil Foto</button>
                    <button type="button" id="retake-btn" class="btn btn-outline-secondary w-100 mb-2 d-none"><i class="bi bi-arrow-clockwise"></i> Ulangi Foto</button>
                    
                    <canvas id="canvas" class="d-none"></canvas>
                    <input type="hidden" name="selfie_image" id="selfie_image">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Surat Dokter / Lampiran (Opsional)</label>
                    <input class="form-control form-control-sm" type="file" name="document" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2" id="submit-btn"><i class="bi bi-send-fill me-1"></i> Kirim Permohonan</button>
            </form>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const photoPreview = document.getElementById('photo-preview');
        const snapBtn = document.getElementById('snap-btn');
        const retakeBtn = document.getElementById('retake-btn');
        const selfieInput = document.getElementById('selfie_image');
        const form = document.getElementById('izinForm');
        let stream = null;

        // Buka Kamera HP/Webcam
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = stream;
            } catch (err) {
                alert("Kamera tidak dapat diakses. Pastikan Anda memberikan izin akses kamera pada browser.");
            }
        }

        startCamera();

        // Ambil Foto
        snapBtn.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            selfieInput.value = dataUrl;
            
            photoPreview.src = dataUrl;
            video.classList.add('d-none');
            photoPreview.classList.remove('d-none');
            
            snapBtn.classList.add('d-none');
            retakeBtn.classList.remove('d-none');
        });

        // Ulangi Foto
        retakeBtn.addEventListener('click', () => {
            selfieInput.value = '';
            video.classList.remove('d-none');
            photoPreview.classList.add('d-none');
            
            snapBtn.classList.remove('d-none');
            retakeBtn.classList.add('d-none');
        });

        // Validasi sebelum submit
        form.addEventListener('submit', (e) => {
            if (!selfieInput.value) {
                e.preventDefault();
                alert("Anda wajib mengambil foto bukti keadaan saat ini sebelum mengirim.");
            }
        });
    </script>
</body>
</html>