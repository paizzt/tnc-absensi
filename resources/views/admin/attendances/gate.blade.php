@extends('layouts.app')

@section('title', 'Scan Gerbang (Kamera)')

@section('content')
<div class="container-fluid p-0 d-flex flex-column" style="min-height: 80vh;">
    <div class="row flex-grow-1 g-4">
        
        <div class="col-md-5 d-flex flex-column bg-white rounded-4 shadow-sm p-4 border">
            <div class="text-center mb-3">
                <h5 class="fw-bold mb-1" style="color: var(--primary);">KAMERA PEMINDAI</h5>
                <p class="text-neutral small">Arahkan QR Code Siswa ke kotak kamera</p>
            </div>
            
            <div id="reader" class="w-100 rounded-3 overflow-hidden border border-2 border-primary border-opacity-25 shadow-inner mb-3" style="background-color: #000;"></div>
            
            <div id="scan_status" class="alert w-100 text-center d-none fw-medium" role="alert"></div>
            
            <div class="mt-auto pt-3 border-top text-center">
                <p class="text-muted small mb-2">Gunakan kamera belakang untuk HP, atau Webcam untuk Laptop.</p>
                <button id="swapCamera" class="btn btn-sm btn-light border text-primary px-4">🔄 Tukar Kamera</button>
            </div>
        </div>

        <div class="col-md-7 d-flex flex-column justify-content-center bg-white rounded-4 shadow-sm p-5 border">
            <div class="text-center" id="result_area">
                <div class="mb-4 position-relative d-inline-block">
                    <img src="https://ui-avatars.com/api/?name=Siswa&background=f3f4f6&color=6B7280&size=150" id="student_photo" class="rounded-circle border shadow-sm" alt="Foto Siswa" style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <div id="success_badge" class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white border-4 d-none" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-white fw-bold fs-5">✓</span>
                    </div>
                </div>
                
                <h2 class="fw-bold mb-1" id="res_name">-</h2>
                <h6 class="text-neutral mb-4" id="res_nis">Menunggu pindaian...</h6>
                
                <div class="p-3 rounded-3" id="res_box" style="background-color: #f8f9fa;">
                    <h4 class="fw-bold mb-0" id="res_msg" style="color: #6B7280;">Siap memindai kehadiran</h4>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        let html5QrCode = new Html5Qrcode("reader");
        let isProcessing = false; // Mencegah double-scan
        let currentFacingMode = "environment"; // Default: Kamera Belakang

        // Konfigurasi visual kamera
        const config = { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        // Fungsi ketika QR berhasil terbaca oleh lensa
        function onScanSuccess(decodedText, decodedResult) {
            if(isProcessing) return; // Jika sedang memproses data sebelumnya, abaikan pindaian baru
            
            isProcessing = true;
            html5QrCode.pause(); // Hentikan kamera sejenak (Freeze)
            
            $('#scan_status').addClass('d-none').removeClass('alert-success alert-danger');
            $('#res_msg').text('Memproses data...').css('color', '#F59E0B');
            
            // Tembakkan ke server via AJAX
            $.ajax({
                url: "{{ route('admin.attendances.scan_process') }}",
                type: 'POST',
                data: { qr_code: decodedText },
                success: function(response) {
                    $('#res_name').text(response.student_name).css('color', '#111827');
                    $('#res_nis').text(response.student_nis + ' | ' + response.classroom);
                    $('#res_msg').text(response.message).css('color', '#16A34A');
                    $('#res_box').css('background-color', '#dcfce7'); // Hijau soft
                    $('#student_photo').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(response.student_name) + '&background=ffffff&color=16A34A&size=150');
                    $('#success_badge').removeClass('d-none');
                    
                    $('#scan_status').text('Berhasil dipindai').addClass('alert-success').removeClass('d-none');
                    
                    resumeScanner();
                },
                error: function(xhr) {
                    let errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                    
                    $('#res_name').text('GAGAL').css('color', '#DC2626');
                    $('#res_nis').text('-');
                    $('#res_msg').text(errMsg).css('color', '#DC2626');
                    $('#res_box').css('background-color', '#fee2e2'); // Merah soft
                    $('#student_photo').attr('src', 'https://ui-avatars.com/api/?name=X&background=ffffff&color=DC2626&size=150');
                    $('#success_badge').addClass('d-none');

                    $('#scan_status').text(errMsg).addClass('alert-danger').removeClass('d-none');
                    
                    resumeScanner();
                }
            });
        }

        function resumeScanner() {
            setTimeout(function() {
                // Kembalikan tampilan ke normal dan lanjutkan scan setelah jeda 2 detik
                $('#success_badge').addClass('d-none');
                $('#res_box').css('background-color', '#f8f9fa');
                isProcessing = false;
                html5QrCode.resume();
            }, 2000); 
        }

        // Fungsi menyalakan kamera
        function startCamera(facingMode) {
            html5QrCode.start(
                { facingMode: facingMode }, 
                config, 
                onScanSuccess
            ).catch(err => {
                $('#scan_status').text('Gagal mengakses kamera. Pastikan izin kamera diberikan.').addClass('alert-danger d-block');
            });
        }

        // Mulai kamera saat halaman dimuat
        startCamera(currentFacingMode);

        // Tombol tukar kamera (Depan/Belakang)
        $('#swapCamera').click(function() {
            html5QrCode.stop().then(ignore => {
                currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                startCamera(currentFacingMode);
            }).catch(err => {
                console.log("Error stopping camera", err);
            });
        });
    });
</script>
@endsection