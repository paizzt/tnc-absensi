<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $target;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $target, string $message)
    {
        $this->target = $target;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = env('FONNTE_TOKEN');

        if (empty($token) || $token === 'masukkan_token_fonnte_anda_di_sini') {
            Log::warning('Fonnte Token belum dikonfigurasi. Pesan ke ' . $this->target . ' dibatalkan.');
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $this->target,
                'message' => $this->message,
                'countryCode' => '62', // Otomatis mengonversi 08 menjadi 628
            ]);

            if (!$response->successful()) {
                Log::error('Gagal mengirim WA via Fonnte: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Exception saat mengirim WA via Fonnte: ' . $e->getMessage());
        }
    }
}