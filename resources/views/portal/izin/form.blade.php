<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Izin - SCANATTEND</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; padding-bottom: 3rem; }
        .camera-container { width: 100%; max-width: 320px; margin: 0 auto; border-radius: 12px; overflow: hidden; position: relative; background: #000; }
        #videoElement { width: 100%; height: auto; transform: scaleX(-1); /* Efek Cermin untuk Kamera Depan */ }
    </style>
</head>
<body>

    <div class="container mt-4 mt-md-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="me-3">
                            <div style="width: 50px; height: 50px; background-color: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #2563EB; font-weight: bold; font-size: 1.2rem;">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $student->name }}</h6>
                            <p class="text-muted small mb-0">{{ $student->nis }} • {{ $student->classroom->name }} • {{ $student->school->name }}</p>
                        </div>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <form id="permissionForm" action="{{ route('portal.izin.submit', $student->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <input type="hidden" name="selfie_image" id="selfie_image" required>

                            <div class="mb-4">
                                <label class="form-label text-dark fw-semibold">Pilih Jenis Halangan <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select form-select-lg" required>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin / Kepentingan Keluarga</option>
                                </select>
                            </div>

                            <div class="mb-4" id="document_container">
                                <label class="form-label text-dark fw-semibold">Lampiran (Surat Dokter / Bukti) <span class="text-danger">*</span></label>
                                <input type="file" name="document" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted">Wajib jika memilih Sakit.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-dark fw-semibold">Keterangan Detail <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required placeholder="Jelaskan alasan secara singkat..."></textarea>
                            </div>

                            <hr class="my-4">

                            <div class="text-center mb-4">
                                <h6 class="fw-bold text-dark mb-2">Verifikasi Keamanan Wajah</h6>
                                <p class="text-muted small mb-3">Sesuai SOP, mohon posisikan wajah siswa dan orang tua di depan kamera. Sistem akan otomatis menangkap gambar saat Anda menekan tombol kirim.</p>
                                
                                <div class="camera-container border border-2 border-primary border-opacity-25 shadow-sm">
                                    <video id="videoElement" autoplay playsinline></video>
                                    <canvas id="canvas" class="d-none"></canvas>
                                </div>
                                <div id="cameraStatus" class="mt-2 small text-warning fw-semibold">Meminta akses kamera...</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" id="btnSubmit" class="btn btn-primary btn-lg fw-medium" style="background-color: #2563EB;" disabled>Kirim Permohonan Izin</button>
                                <a href="{{ route('portal.izin.index') }}" class="btn btn-light btn-lg text-muted fw-medium">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Togle kewajiban dokumen berdasarkan tipe
            $('#type').change(function() {
                if ($(this).val() === 'Izin') {
                    $('#document_container label span').hide();
                } else {
                    $('#document_container label span').show();
                }
            });

            // Inisialisasi Kamera Depan
            const video = document.getElementById('videoElement');
            const canvas = document.getElementById('canvas');
            const btnSubmit = document.getElementById('btnSubmit');
            const cameraStatus = document.getElementById('cameraStatus');
            const form = document.getElementById('permissionForm');
            const selfieInput = document.getElementById('selfie_image');

            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                .then(function (stream) {
                    video.srcObject = stream;
                    cameraStatus.textContent = "Kamera aktif. Wajah siap di-scan.";
                    cameraStatus.className = "mt-2 small text-success fw-semibold";
                    btnSubmit.disabled = false; // Buka tombol jika kamera sudah jalan
                })
                .catch(function (error) {
                    cameraStatus.textContent = "Akses kamera ditolak. Izin tidak dapat dilanjutkan.";
                    cameraStatus.className = "mt-2 small text-danger fw-semibold";
                });
            } else {
                cameraStatus.textContent = "Browser tidak mendukung fitur kamera.";
                cameraStatus.className = "mt-2 small text-danger fw-semibold";
            }

            // Aksi saat tombol Kirim ditekan
            btnSubmit.addEventListener('click', function() {
                // Pastikan kamera nyala
                if(video.srcObject) {
                    // Set ukuran canvas sesuai resolusi video
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    
                    // Ambil frame/gambar dari video
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    // Konversi ke format DataURL (Base64)
                    const dataURL = canvas.toDataURL('image/jpeg', 0.8);
                    
                    // Masukkan ke input tersembunyi
                    selfieInput.value = dataURL;

                    // Ubah teks tombol dan submit form
                    btnSubmit.innerHTML = "Memproses Pengiriman...";
                    btnSubmit.disabled = true;
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>