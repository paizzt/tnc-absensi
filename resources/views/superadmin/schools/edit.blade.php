@extends('layouts.app')

@section('title', 'Edit Sekolah')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #111827;">Edit Data Sekolah</h4>
            <p class="text-neutral small mb-0">Perbarui informasi profil dasar sekolah.</p>
        </div>
        <a href="{{ route('schools.index') }}" class="btn btn-light btn-sm px-3 border">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 max-w-3xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('schools.update', $school->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">NPSN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('npsn') is-invalid @enderror" name="npsn" value="{{ old('npsn', $school->npsn) }}" required>
                        @error('npsn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Nama Sekolah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $school->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-neutral small fw-semibold">Email Sekolah</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $school->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-neutral small fw-semibold">Nomor Telepon</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $school->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label text-neutral small fw-semibold">Alamat Lengkap</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3">{{ old('address', $school->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection