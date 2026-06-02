<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pengajuan Izin - SCANATTEND</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .hero-section { background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%); padding: 3rem 0; color: white; margin-bottom: -3rem; }
    </style>
</head>
<body>

    <div class="hero-section text-center pb-5">
        <h2 class="fw-bold mb-2">Portal Layanan Izin Siswa</h2>
        <p class="text-white-50">Sistem Informasi SCANATTEND</p>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 mt-n4">
                    <div class="card-body p-4 p-md-5">
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <h5 class="fw-bold text-dark mb-4 text-center">Verifikasi Identitas</h5>
                        <form action="{{ route('portal.izin.search') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-semibold">Masukkan Nomor Induk Siswa (NIS)</label>
                                <input type="text" name="nis" class="form-control form-control-lg text-center fw-bold" placeholder="Contoh: 12345" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-medium" style="background-color: #2563EB;">Lanjutkan</button>
                        </form>
                    </div>
                </div>
                <p class="text-center text-muted small mt-4">Layanan ini membutuhkan akses ke kamera untuk keperluan verifikasi keamanan (mencegah pemalsuan).</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>