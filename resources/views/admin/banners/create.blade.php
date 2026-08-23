@extends('layouts.app')

@section('title', 'Tambah Banner Iklan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Tambah Banner Baru</h4>
            <p class="text-neutral small mb-0">Unggah gambar untuk slider halaman login.</p>
        </div>
        <a href="{{ route('banners.index') }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 max-w-2xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label text-neutral small fw-semibold">Judul Banner (Opsional)</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="Contoh: Promo Pendaftaran 2026">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-neutral small fw-semibold">Link Tautan (Opsional)</label>
                    <input type="url" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link') }}" placeholder="Contoh: https://contoh.com/promo">
                    <div class="form-text small">Masukkan link jika banner ini bisa diklik.</div>
                    @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-neutral small fw-semibold">File Gambar Banner <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*" required>
                    <div class="form-text small">Rekomendasi ukuran: Resolusi tinggi (Misal 1080x1920) dengan orientasi Potrait agar sesuai di split screen. Maksimal 2MB (JPG/PNG).</div>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Urutan Tampil <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" name="order" value="{{ old('order', 0) }}" required min="0">
                        <div class="form-text small">Angka terkecil akan tampil lebih dulu.</div>
                        @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch fs-5 mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label fs-6 ms-2 text-dark" for="is_active">Langsung Aktifkan?</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end border-top pt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan & Unggah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
