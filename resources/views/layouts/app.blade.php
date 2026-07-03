<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - App Absensi</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root { 
            --primary: #2563EB; 
            --primary-hover: #1d4ed8;
            --sidebar-bg: #ffffff;
            --sidebar-color: #6B7280;
            --sidebar-active-bg: #eff6ff;
            --sidebar-active-color: #2563EB;
            --sidebar-width: 250px; 
            --sidebar-collapsed-width: 80px; 
        }
        
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; overflow-x: hidden; }

        #sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0; background: var(--sidebar-bg); border-right: 1px solid #e5e7eb; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1040; display: flex; flex-direction: column; overflow-x: hidden; box-shadow: 2px 0 10px rgba(0,0,0,0.02); }
        .sidebar-brand { height: 70px; display: flex; align-items: center; justify-content: center; padding: 10px 1.5rem; border-bottom: 1px solid #f3f4f6; transition: all 0.3s ease; }
        .sidebar-nav { padding: 1rem 0; list-style: none; margin: 0; flex-grow: 1; overflow-y: auto; overflow-x: hidden; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }

        .nav-label { font-size: 0.75rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; padding: 1rem 1.5rem 0.5rem; white-space: nowrap; transition: opacity 0.3s; }
        .sidebar-nav a { display: flex; align-items: center; padding: 0.75rem 1.5rem; color: var(--sidebar-color); text-decoration: none; font-size: 0.95rem; font-weight: 500; white-space: nowrap; transition: all 0.2s ease-in-out; border-left: 4px solid transparent; }
        .sidebar-nav a i { font-size: 1.25rem; margin-right: 14px; min-width: 24px; text-align: center; transition: margin 0.3s ease; }
        .sidebar-nav a:hover { color: var(--sidebar-active-color); background-color: #f8fafc; }
        .sidebar-nav a.active { color: var(--sidebar-active-color); background-color: var(--sidebar-active-bg); border-left: 4px solid var(--primary); font-weight: 600; }

        #sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        #sidebar.collapsed .sidebar-brand { padding: 10px 0; }
        #sidebar.collapsed .nav-text, #sidebar.collapsed .nav-label { opacity: 0; display: none; }
        #sidebar.collapsed .sidebar-nav a { padding: 0.8rem 0; justify-content: center; }
        #sidebar.collapsed .sidebar-nav a i { margin-right: 0; font-size: 1.4rem; }

        #content-wrapper { margin-left: var(--sidebar-width); transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); min-height: 100vh; display: flex; flex-direction: column; }
        #content-wrapper.expanded { margin-left: var(--sidebar-collapsed-width); }

        #topbar { height: 70px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; padding: 0 1.5rem; justify-content: space-between; position: sticky; top: 0; z-index: 1030; }
        #sidebarToggle { background: transparent; border: none; font-size: 1.5rem; color: var(--sidebar-color); cursor: pointer; padding: 0; transition: color 0.2s; }
        #sidebarToggle:hover { color: var(--primary); }
        .main-content { padding: 2rem 1.5rem; flex-grow: 1; }

        @media (max-width: 768px) { 
            #sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; } 
            #sidebar.show { transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,0.1); } 
            #content-wrapper, #content-wrapper.expanded { margin-left: 0 !important; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 1035; backdrop-filter: blur(2px); }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>
    
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <nav id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-height: 45px; width: auto; object-fit: contain;">
        </div>
        
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="bi bi-grid-fill"></i> <span class="nav-text">Dashboard</span>
                </a>
            </li>
            
            @hasanyrole('Super Admin|Admin Sekolah')
                <li class="nav-label">Manajemen Utama</li>
                <li>
                    <a href="{{ route('schools.index') }}" class="{{ request()->routeIs('schools.*') ? 'active' : '' }}" title="Profil Sekolah">
                        <i class="bi bi-buildings"></i> <span class="nav-text">Profil Sekolah</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}" title="Data Pengguna">
                        <i class="bi bi-people-fill text-primary"></i> <span class="nav-text">Data Pengguna</span>
                    </a>
                </li>
            @endhasanyrole

            @hasanyrole('Super Admin|Admin Sekolah|Petugas Piket')
                <li class="nav-label">Operasional Gerbang</li>
                <li>
                    <a href="{{ route('admin.attendances.gate') }}" class="{{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}" title="Scan Gerbang">
                        <i class="bi bi-qr-code-scan"></i> <span class="nav-text">Scan Gerbang (Live)</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}" title="Data Siswa">
                        <i class="bi bi-person-vcard"></i> <span class="nav-text">Data Siswa</span>
                    </a>
                </li>
                
                <li class="nav-label">Master Data Akademik</li>
                <li>
                    <a href="{{ route('admin.schedules.index') }}" class="{{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}" title="Jadwal Pelajaran">
                        <i class="bi bi-calendar-week"></i> <span class="nav-text">Jadwal Pelajaran</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.classrooms.index') }}" class="{{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}" title="Master Kelas">
                        <i class="bi bi-door-open"></i> <span class="nav-text">Master Kelas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.subjects.index') }}" class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}" title="Master Mapel">
                        <i class="bi bi-book"></i> <span class="nav-text">Master Mapel</span>
                    </a>
                </li>
                
                <li class="nav-label">Laporan & Rekap</li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" title="Export Laporan">
                        <i class="bi bi-printer text-warning"></i> <span class="nav-text">Export Laporan</span>
                    </a>
                </li>
            @endhasanyrole

            @hasanyrole('Super Admin|Guru')
                <li class="nav-label">Portal Guru</li>
                <li>
                    <a href="{{ route('teacher.attendances.index') }}" class="{{ request()->routeIs('teacher.attendances.*') ? 'active' : '' }}" title="Absensi Kelas">
                        <i class="bi bi-clipboard-check text-success"></i> <span class="nav-text">Jadwal Mengajar Saya</span>
                    </a>
                </li>
                
                @php
                    $isWaliKelas = \App\Models\Classroom::where('teacher_id', Auth::id())->exists();
                @endphp

                @if(Auth::user()->hasRole('Super Admin') || $isWaliKelas)
                    <li>
                        <a href="{{ route('teacher.permissions.index') }}" class="{{ request()->routeIs('teacher.permissions.*') ? 'active' : '' }}" title="Validasi Izin Siswa">
                            <i class="bi bi-envelope-paper text-primary"></i> <span class="nav-text">Validasi Izin Siswa</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teacher.exits.index') }}" class="{{ request()->routeIs('teacher.exits.*') ? 'active' : '' }}" title="Izin Keluar Sementara">
                            <i class="bi bi-door-open-fill text-warning"></i> <span class="nav-text">Izin Keluar (Gate Pass)</span>
                        </a>
                    </li>
                @endif
            @endhasanyrole

            @hasanyrole('Super Admin|Guru BK|Kepala Sekolah')
                <li class="nav-label">Bimbingan Konseling</li>
                <li>
                    <a href="{{ route('bk.dashboard') }}" class="{{ request()->routeIs('bk.*') ? 'active' : '' }}" title="Evaluasi & Surat SP">
                        <i class="bi bi-shield-exclamation text-danger"></i> <span class="nav-text">Evaluasi & Surat SP</span>
                    </a>
                </li>
            @endhasanyrole

            @hasanyrole('Super Admin|Admin Sekolah|Petugas Piket|Guru BK|Kepala Sekolah')
                <li class="nav-label">Sistem & Konfigurasi</li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="Pengaturan Sekolah">
                        <i class="bi bi-gear"></i> <span class="nav-text">Pengaturan Sekolah</span>
                    </a>
                </li>
            @endhasanyrole
        </ul>
        
        <div class="mt-auto p-3 border-top text-center" id="sidebarProfile">
            <div class="d-flex align-items-center justify-content-center">
                <div style="width:35px; height:35px; background:var(--primary-hover); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold;" class="flex-shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="nav-text ms-2 text-start" style="line-height:1.2; overflow:hidden;">
                    <div class="fw-bold text-dark text-truncate" style="font-size:0.85rem;">{{ Auth::user()->name }}</div>
                    <div class="text-neutral text-truncate" style="font-size:0.75rem;">{{ Auth::user()->roles->pluck('name')->first() }}</div>
                </div>
            </div>
        </div>
    </nav>

    <div id="content-wrapper">
        <header id="topbar">
            <div class="d-flex align-items-center">
                <button id="sidebarToggle" title="Buka/Tutup Menu">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 ms-3 fw-bold text-dark d-none d-md-block" style="opacity: 0.8;">@yield('title')</h5>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light border text-danger fw-medium px-3 rounded-pill shadow-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> <span class="d-none d-sm-inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>
        
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const wrapper = $('#content-wrapper');
            const overlay = $('#sidebarOverlay');
            const toggleBtn = $('#sidebarToggle');

            if(localStorage.getItem('sidebarState') === 'collapsed' && $(window).width() > 768) {
                sidebar.addClass('collapsed');
                wrapper.addClass('expanded');
            }

            toggleBtn.on('click', function() {
                if ($(window).width() > 768) {
                    sidebar.toggleClass('collapsed');
                    wrapper.toggleClass('expanded');
                    
                    if(sidebar.hasClass('collapsed')) {
                        localStorage.setItem('sidebarState', 'collapsed');
                    } else {
                        localStorage.setItem('sidebarState', 'expanded');
                    }
                } else {
                    sidebar.toggleClass('show');
                    overlay.toggleClass('show');
                }
            });

            overlay.on('click', function() {
                sidebar.removeClass('show');
                overlay.removeClass('show');
            });
            
            const tooltipTriggerList = document.querySelectorAll('[title]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'right',
                trigger: 'hover',
                fallbackPlacements: ['right', 'bottom']
            }));
        });
    </script>
    
    @yield('scripts')
</body>
</html>