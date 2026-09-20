<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - ABSENSI Enterprise</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f3f4f6; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
            margin: 0;
        }
        .login-card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); 
            width: 100%; 
            max-width: 400px; 
            padding: 2.5rem 2rem; 
            background: #fff; 
        }
        .btn-primary { background-color: #2563EB; border-color: #2563EB; font-weight: 500; }
        .btn-primary:hover { background-color: #1d4ed8; border-color: #1d4ed8; }
        .form-control:focus { border-color: #2563EB; box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25); }
        .text-neutral { color: #6B7280; }
    </style>
</head>
<body>
    
    <div class="login-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #2563EB;">Lupa Sandi?</h3>
            <p class="text-neutral small">Masukkan email yang terdaftar untuk menerima tautan pemulihan kata sandi.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="background-color: #fef2f2; border-color: #fecaca; color: #DC2626;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="form-label text-neutral small fw-semibold">Alamat Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="superadmin@absensi.com">
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">Kirim Tautan Pemulihan</button>
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none small fw-medium" style="color: #6B7280;"><i class="bi bi-arrow-left"></i> Kembali ke Login</a>
            </div>
        </form>
    </div>
    
</body>
</html>
