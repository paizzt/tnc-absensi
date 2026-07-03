@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm rounded-4 bg-white mx-auto" style="max-width: 800px;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-gear text-warning me-2"></i>Edit Akun Pengguna</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-dark fw-semibold small">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small">Alamat Email (Login Utama)</label>
                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-dark fw-semibold small">Kata Sandi Baru</label>
                        <input type="password" class="form-control" name="password" minlength="8" placeholder="Kosongkan jika tidak ingin diubah">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark fw-semibold small">Peran (Role)</label>
                        <select class="form-select border-primary" name="role" id="roleSelect" required>
                            @php $userRole = $user->roles->first()->name ?? ''; @endphp
                            <option value="">-- Pilih Peran Pengguna --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $userRole == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="guruSettings" class="{{ $userRole == 'Guru' ? '' : 'd-none' }} bg-light p-3 rounded-3 border mb-4">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-gear-wide-connected me-1"></i>Penugasan Akademik (Opsional)</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label text-dark fw-semibold small">Wali Kelas di Kelas:</label>
                            <select class="form-select" name="classroom_id">
                                <option value="">-- Bukan Wali Kelas --</option>
                                @foreach($classrooms as $class)
                                    <option value="{{ $class->id }}" {{ ($user->homeroomClass && $user->homeroomClass->id == $class->id) ? 'selected' : '' }}>
                                        {{ $class->level }} - {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold small">Mengajar Mata Pelajaran:</label>
                            @php
                                $userSubjectIds = $user->subjects->pluck('id')->toArray();
                            @endphp
                            <select class="form-select" name="subject_ids[]" multiple style="height: 120px;">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ in_array($subject->id, $userSubjectIds) ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">Tahan tombol CTRL (Windows) atau CMD (Mac) untuk memilih lebih dari satu mapel.</div>
                        </div>
                    </div>
                </div>

                <div class="text-end border-top pt-3">
                    <a href="{{ route('users.index') }}" class="btn btn-light border px-4 me-2">Batal</a>
                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4"><i class="bi bi-save me-1"></i> Update Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#roleSelect').on('change', function() {
            if ($(this).val() === 'Guru') {
                $('#guruSettings').removeClass('d-none');
            } else {
                $('#guruSettings').addClass('d-none');
                $('select[name="classroom_id"]').val('');
                $('select[name="subject_ids[]"]').val([]);
            }
        });
    });
</script>
@endsection