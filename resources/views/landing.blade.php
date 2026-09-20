<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arjuna Production Group</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo-arjuna-one.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Polka dot pattern */
        .bg-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(#bae6fd 2px, transparent 2px);
            background-size: 30px 30px;
            opacity: 0.6;
            z-index: -1;
        }

        /* Hero Section */
        .hero-section {
            padding: 3rem 1rem 2rem;
            text-align: center;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            max-height: 80px;
            object-fit: contain;
        }

        .hero-title {
            font-weight: 800;
            font-size: 2.2rem;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
            color: #0f172a;
            text-transform: uppercase;
        }

        .hero-subtitle {
            font-weight: 700;
            font-size: 1.1rem;
            color: #334155;
            margin-bottom: 2.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Card Menu Styling (1 Warna) */
        .menu-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-decoration: none;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            border: 1px solid rgba(37, 99, 235, 0.2);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .menu-card-maroon {
            background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%);
            border: 1px solid rgba(153, 27, 27, 0.2);
            box-shadow: 0 10px 15px -3px rgba(153, 27, 27, 0.2);
        }

        .menu-card.menu-card-maroon:hover {
            box-shadow: 0 20px 25px -5px rgba(153, 27, 27, 0.4);
        }

        .menu-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .menu-title {
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        .menu-desc {
            font-size: 0.8rem;
            opacity: 0.8;
            font-weight: 300;
            text-align: center;
        }

        .footer {
            margin-top: auto;
            padding: 1.5rem 0;
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
        }

        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #2563EB;
            color: white;
            text-decoration: none;
            margin: 0 0.3rem;
            transition: transform 0.2s, background-color 0.2s;
        }

        .social-icons a:hover {
            transform: scale(1.1);
            background-color: #1d4ed8;
            color: white;
        }
        
        @media (max-width: 768px) {
            .hero-title { font-size: 1.8rem; }
            .hero-subtitle { font-size: 0.9rem; }
            .logo-container img { max-height: 60px; }
            .menu-card { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>

    <div class="bg-pattern"></div>

    <div class="container flex-grow-1 d-flex flex-column">
        
        <div class="hero-section">
            <div class="logo-container">
                <img src="{{ asset('images/logo-ajr.png') }}" alt="AJR Logo">
                <img src="{{ asset('images/logo-ragiragi.png') }}" alt="Ragi Ragi Logo">
                <img src="{{ asset('images/logo-arjuna-one.png') }}" alt="Arjuna One Logo">
            </div>
            <h1 class="hero-title">Arjuna Production Group</h1>
            <p class="hero-subtitle">Inovasi - Kreatifitas - Kreadibilitas</p>
        </div>

        <div class="row justify-content-center g-4 max-w-4xl mx-auto mb-5" style="max-width: 900px; width: 100%;">
            
            <!-- Menu 1 -->
            <div class="col-md-6 col-lg-3 col-sm-6 col-10 mx-auto mx-sm-0">
                <a href="https://arjunaproductionsgruphome1.odoo.com/" target="_blank" class="menu-card">
                    <i class="bi bi-globe menu-icon"></i>
                    <h3 class="menu-title">Arjuna Group</h3>
                    <span class="menu-desc">website resmi</span>
                </a>
            </div>

            <!-- Menu 2 -->
            <div class="col-md-6 col-lg-3 col-sm-6 col-10 mx-auto mx-sm-0">
                <a href="https://arjuna-productions-grup.odoo.com/" target="_blank" class="menu-card menu-card-maroon">
                    <i class="bi bi-printer menu-icon"></i>
                    <h3 class="menu-title">Ragi Ragi ID</h3>
                    <span class="menu-desc">bordir-sablon-printing</span>
                </a>
            </div>

            <!-- Menu Login -->
            <div class="col-md-6 col-lg-3 col-sm-6 col-10 mx-auto mx-sm-0">
                <a href="{{ route('login') }}" class="menu-card">
                    <i class="bi bi-person-workspace menu-icon"></i>
                    <h3 class="menu-title">Login App</h3>
                    <span class="menu-desc">Akses Sistem Absensi</span>
                </a>
            </div>

            <!-- Menu Izin -->
            <div class="col-md-6 col-lg-3 col-sm-6 col-10 mx-auto mx-sm-0">
                <a href="{{ route('portal.izin.index') }}" class="menu-card">
                    <i class="bi bi-envelope-paper menu-icon"></i>
                    <h3 class="menu-title">Izin Siswa</h3>
                    <span class="menu-desc">Formulir Pengajuan Izin</span>
                </a>
            </div>

        </div>
    </div>

    <footer class="footer">
        <div class="social-icons mb-2">
            <a href="https://www.instagram.com/ragiragi.id?igsh=bTg2aTByM2N3OHUy&utm_source=qr" target="_blank"><i class="bi bi-instagram"></i></a>
            <a href="#" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="#" target="_blank"><i class="bi bi-youtube"></i></a>
            <a href="https://share.google/Kg3ENq9M9Ej9RHVdb" target="_blank" title="Lokasi Kami"><i class="bi bi-geo-alt-fill"></i></a>
        </div>
        &copy; {{ date('Y') }} Arjuna Production Group. All rights reserved.
    </footer>

</body>
</html>
