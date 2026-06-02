@extends('layouts.app')

@section('title', 'Data Pengguna')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #111827;">Manajemen Pengguna</h4>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-3">+ Tambah Pengguna</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #16A34A;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">NAMA & EMAIL</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">ROLE</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">SEKOLAH</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm">STATUS</th>
                            <th class="border-0 px-4 py-3 text-neutral fw-semibold text-sm text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-medium text-dark">{{ $user->name }}</div>
                                <div class="text-neutral small">{{ $user->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-primary text-white bg-opacity-75">{{ $user->roles->pluck('name')->first() ?? 'Belum ada' }}</span>
                            </td>
                            <td class="px-4 py-3 text-neutral small">
                                {{ $user->school ? $user->school->name : 'Global (Semua Sekolah)' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-75">Aktif</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-75">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-light border text-primary">Edit</a>
                                    
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger">Hapus</button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light border text-muted" disabled title="Tidak bisa menghapus akun sendiri">Hapus</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-neutral">Belum ada data pengguna.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-top">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection