<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SCANATTEND Enterprise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); width: 100%; max-width: 400px; padding: 2rem; background: #fff; }
        .btn-primary { background-color: #2563EB; border-color: #2563EB; font-weight: 500; }
        .btn-primary:hover { background-color: #1d4ed8; border-color: #1d4ed8; }
        .form-control:focus { border-color: #2563EB; box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25); }
        .text-neutral { color: #6B7280; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #2563EB;">SCANATTEND</h3>
            <p class="text-neutral small">Sistem Absensi Digital Terintegrasi</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="background-color: #fef2f2; border-color: #fecaca; color: #DC2626;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('authenticate') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label text-neutral small fw-semibold">Alamat Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="superadmin@scanattend.com">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label text-neutral small fw-semibold">Kata Sandi</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Masuk ke Sistem</button>
        </form>
    </div>
</body>
</html>