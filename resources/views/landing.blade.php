<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Digital Terpadu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Slate 900 */
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Latar belakang dengan pola dot pattern */
        .bg-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(#334155 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.5;
            z-index: -1;
        }

        .hero-section {
            padding: 4rem 1rem 2rem;
            text-align: center;
        }

        .hero-title {
            font-weight: 800;
            font-size: 2.5rem;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            color: #f8fafc;
            text-transform: uppercase;
        }

        .hero-subtitle {
            font-weight: 400;
            font-size: 1.1rem;
            color: #94a3b8;
            margin-bottom: 3rem;
            letter-spacing: 2px;
        }

        /* Card Menu Styling (1 Warna) */
        .menu-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
            border-radius: 16px;
            padding: 2.5rem 1.5rem;
            text-decoration: none;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .menu-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .menu-title {
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-desc {
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 300;
            text-align: center;
        }

        .footer {
            margin-top: auto;
            padding: 2rem 0;
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .menu-card { padding: 2rem 1rem; }
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>

    <div class="container flex-grow-1 d-flex flex-column justify-content-center">
        <div class="hero-section">
            <h1 class="hero-title">Layanan Digital Terpadu</h1>
            <p class="hero-subtitle">"Moderasi - Inovasi - Inspirasi"</p>
        </div>

        <div class="row justify-content-center g-4 max-w-4xl mx-auto" style="max-width: 800px; width: 100%;">
            
            <!-- Menu Login -->
            <div class="col-md-6 col-sm-10">
                <a href="{{ route('login') }}" class="menu-card">
                    <i class="bi bi-person-workspace menu-icon"></i>
                    <h3 class="menu-title">Portal Login</h3>
                    <span class="menu-desc">Akses Dashboard, Absensi Kelas & Pengaturan Sistem</span>
                </a>
            </div>

            <!-- Menu Izin -->
            <div class="col-md-6 col-sm-10">
                <a href="{{ route('portal.izin.index') }}" class="menu-card">
                    <i class="bi bi-envelope-paper menu-icon"></i>
                    <h3 class="menu-title">Izin / Sakit</h3>
                    <span class="menu-desc">Formulir Pengajuan Izin Siswa oleh Orang Tua Wali</span>
                </a>
            </div>

        </div>
    </div>

    <footer class="footer">
        &copy; {{ date('Y') }} Layanan Digital Terpadu. All rights reserved.
    </footer>

</body>
</html>
