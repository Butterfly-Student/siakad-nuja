<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotifikasiWhatsapp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappGatewayService
{
    private string $baseUrl;
    private string $session;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('whatsapp.url', 'http://localhost:3000'), '/');
        $this->session = config('whatsapp.session', 'default');
        $this->apiKey  = config('whatsapp.api_key', '');
        $this->timeout = config('whatsapp.timeout', 30);
    }

    /**
     * Normalisasi nomor HP ke format internasional 628xxx (tanpa tanda +).
     * WAHA membutuhkan format "628xxx@c.us" sebagai chatId.
     */
    public function normalisasiNomor(string $noHp): string
    {
        // Hapus karakter selain angka
        $noHp = preg_replace('/[^0-9]/', '', $noHp);

        // Ganti awalan 0 dengan 62
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        // Jika sudah ada 62 di depan, biarkan
        return $noHp;
    }

    /**
     * Format nomor ke chatId WAHA (e.g., "628123456789@c.us")
     */
    private function toChatId(string $noHp): string
    {
        return $this->normalisasiNomor($noHp) . '@c.us';
    }

    /**
     * Kirim pesan teks via WAHA API.
     */
    public function send(string $noHp, string $pesan): bool
    {
        try {
            $response = $this->makeRequest('POST', '/api/sendText', [
                'session' => $this->session,
                'chatId'  => $this->toChatId($noHp),
                'text'    => $pesan,
            ]);

            if ($response->successful()) {
                Log::info("[WAHA] Berhasil kirim ke {$noHp}");
                return true;
            }

            Log::error("[WAHA] Gagal kirim ke {$noHp}: HTTP " . $response->status() . ' — ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("[WAHA] Exception kirim ke {$noHp}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan dan catat ke tabel notifikasi_whatsapp.
     */
    public function sendNotification(
        string $noHp,
        string $pesan,
        string $jenis,
        ?int $orangTuaId = null,
        ?int $siswaId = null
    ): bool {
        // Catat ke DB dulu sebagai pending
        $log = NotifikasiWhatsapp::create([
            'orang_tua_id' => $orangTuaId,
            'siswa_id'     => $siswaId,
            'no_tujuan'    => $this->normalisasiNomor($noHp),
            'jenis'        => $jenis,
            'isi_pesan'    => $pesan,
            'status'       => 'pending',
        ]);

        $success = $this->send($noHp, $pesan);

        // Update status log
        $log->update([
            'status'       => $success ? 'terkirim' : 'gagal',
            'dikirim_pada' => $success ? now() : null,
        ]);

        return $success;
    }

    /**
     * Cek status koneksi WAHA untuk sesi aktif.
     * Return: 'CONNECTED' | 'DISCONNECTED' | 'SCAN_QR' | 'STARTING' | 'ERROR'
     */
    public function getStatus(): array
    {
        try {
            $response = $this->makeRequest('GET', "/api/sessions/{$this->session}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status'  => $data['status'] ?? 'UNKNOWN',
                    'name'    => $data['me']['pushName'] ?? null,
                    'phone'   => $data['me']['id'] ?? null,
                    'session' => $this->session,
                ];
            }

            return ['status' => 'DISCONNECTED', 'session' => $this->session];
        } catch (\Exception $e) {
            Log::warning('[WAHA] Tidak bisa cek status: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    /**
     * Ambil screenshot QR code dari WAHA.
     * Return base64 PNG string atau null jika tidak tersedia.
     */
    public function getQrCode(): ?string
    {
        try {
            $response = $this->makeRequest('GET', "/api/{$this->session}/auth/qr", [], [
                'Accept' => 'image/png',
            ]);

            if ($response->successful()) {
                return base64_encode($response->body());
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('[WAHA] Tidak bisa ambil QR: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kirim ulang notifikasi yang gagal.
     */
    public function resendNotification(NotifikasiWhatsapp $notif): bool
    {
        $success = $this->send($notif->no_tujuan, $notif->isi_pesan);

        $notif->update([
            'status'        => $success ? 'terkirim' : 'gagal',
            'dikirim_pada'  => $success ? now() : null,
            'error_message' => $success ? null : 'Retry gagal pada ' . now(),
        ]);

        return $success;
    }

    /**
     * Helper untuk HTTP request ke WAHA dengan API key opsional.
     */
    private function makeRequest(string $method, string $endpoint, array $body = [], array $headers = [])
    {
        $request = Http::timeout($this->timeout);

        if ($this->apiKey) {
            $request = $request->withHeaders(['X-Api-Key' => $this->apiKey]);
        }

        if ($headers) {
            $request = $request->withHeaders($headers);
        }

        $url = $this->baseUrl . $endpoint;

        return match (strtoupper($method)) {
            'GET'  => $request->get($url),
            'POST' => $request->post($url, $body),
            default => throw new \InvalidArgumentException("Method {$method} tidak didukung"),
        };
    }
}
