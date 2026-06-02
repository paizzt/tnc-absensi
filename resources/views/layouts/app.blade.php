<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - SCANATTEND</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary: #2563EB; --success: #16A34A; --warning: #F59E0B; --danger: #DC2626; --neutral: #6B7280; --sidebar-width: 220px; }
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; overflow-x: hidden; }
        #sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0; background: #ffffff; border-right: 1px solid #e5e7eb; transition: all 0.3s; z-index: 1040; }
        .sidebar-brand { height: 60px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary); font-size: 1.25rem; border-bottom: 1px solid #e5e7eb; }
        .sidebar-nav { padding: 1rem 0; list-style: none; margin: 0; }
        .sidebar-nav a { display: block; padding: 0.6rem 1.5rem; color: var(--neutral); text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .sidebar-nav a:hover, .sidebar-nav a.active { color: var(--primary); background-color: #eff6ff; border-right: 3px solid var(--primary); }
        #content-wrapper { margin-left: var(--sidebar-width); transition: all 0.3s; min-height: 100vh; display: flex; flex-direction: column; }
        #topbar { height: 60px; background: #ffffff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; padding: 0 1.5rem; justify-content: space-between; }
        .main-content { padding: 1.5rem; flex-grow: 1; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        @media (max-width: 768px) { #sidebar { transform: translateX(-100%); } #sidebar.show { transform: translateX(0); box-shadow: 4px 0 10px rgba(0,0,0,0.1); } #content-wrapper { margin-left: 0; } }
    </style>
</head>
<body>
    <nav id="sidebar">
        <div class="sidebar-brand">SCANATTEND</div>
        <ul class="sidebar-nav">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            
            @role('Super Admin')
                <li><a href="{{ route('schools.index') }}" class="{{ request()->routeIs('schools.*') ? 'active' : '' }}">Master Sekolah</a></li>
                <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Data Pengguna</a></li>
            @endrole

            @role('Admin Sekolah')
                <li><a href="{{ route('admin.attendances.gate') }}" class="{{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">Scan Gerbang (Live)</a></li>
                <li><a href="{{ route('admin.schedules.index') }}" class="{{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">Jadwal Pelajaran</a></li>
                <li><a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">Data Siswa</a></li>
                <li><a href="{{ route('admin.classrooms.index') }}" class="{{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}">Master Kelas</a></li>
                <li><a href="{{ route('admin.subjects.index') }}" class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">Master Mapel</a></li>
                <li><a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Pengaturan Sekolah</a></li>
            @endrole

            @role('Guru Mata Pelajaran|Wali Kelas')
                <li><a href="{{ route('teacher.attendances.index') }}" class="{{ request()->routeIs('teacher.attendances.*') ? 'active' : '' }}">Absensi Kelas</a></li>
                <li><a href="{{ route('teacher.permissions.index') }}" class="{{ request()->routeIs('teacher.permissions.*') ? 'active' : '' }}">Validasi Izin</a></li>
            @endrole

            @role('Guru BK|Kepala Sekolah')
                <li><a href="{{ route('bk.dashboard') }}" class="{{ request()->routeIs('bk.*') ? 'active' : '' }}">Evaluasi & SP</a></li>
            @endrole
        </ul>
    </nav>

    <div id="content-wrapper">
        <header id="topbar">
            <div><button class="btn btn-sm btn-light d-md-none border" id="sidebarToggle">☰</button></div>
            <div class="d-flex align-items-center">
                <span class="text-neutral small fw-semibold me-3">{{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            </div>
        </header>
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>document.getElementById('sidebarToggle').addEventListener('click', function() { document.getElementById('sidebar').classList.toggle('show'); });</script>
    @yield('scripts')
</body>
</html>