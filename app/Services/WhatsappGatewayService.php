<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotifikasiWhatsapp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappGatewayService
{
    private string $baseUrl;
    private string $deviceId;
    private string $username;
    private string $password;
    private string $phoneSuffix;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl     = rtrim(config('whatsapp.url', 'http://localhost:3000'), '/');
        $this->deviceId    = config('whatsapp.device_id', '');
        $this->username    = config('whatsapp.username', '');
        $this->password    = config('whatsapp.password', '');
        $this->phoneSuffix = config('whatsapp.phone_suffix', '@s.whatsapp.net');
        $this->timeout     = config('whatsapp.timeout', 30);
    }

    /**
     * Normalisasi nomor HP ke format internasional 628xxx (tanpa tanda +).
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
     * Format nomor ke JID Go-WA (e.g., "628123456789@s.whatsapp.net")
     */
    private function toJid(string $noHp): string
    {
        $normalized = $this->normalisasiNomor($noHp);

        // Jika sudah mengandung @, kembalikan langsung
        if (str_contains($normalized, '@')) {
            return $normalized;
        }

        return $normalized . $this->phoneSuffix;
    }

    /**
     * Kirim pesan teks via Go-WA API.
     *
     * Go-WA Endpoint: POST /send/message
     * Body: { "phone": "628xxx@s.whatsapp.net", "message": "text" }
     * Header: X-Device-Id (opsional)
     */
    public function send(string $noHp, string $pesan): bool
    {
        try {
            $response = $this->makeRequest('POST', '/send/message', [
                'phone'   => $this->toJid($noHp),
                'message' => $pesan,
            ]);

            if ($response->successful()) {
                Log::info("[GOWA] Berhasil kirim ke {$noHp}");
                return true;
            }

            Log::error("[GOWA] Gagal kirim ke {$noHp}: HTTP " . $response->status() . ' — ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("[GOWA] Exception kirim ke {$noHp}: " . $e->getMessage());
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
     * Cek status koneksi Go-WA.
     *
     * Go-WA Endpoint: GET /app/status
     * Response: { status: 200, code: "SUCCESS", results: { is_connected, is_logged_in, device_id, jid } }
     *
     * Return array dengan key: 'status', 'is_connected', 'is_logged_in', 'jid', 'device_id'
     */
    public function getStatus(): array
    {
        try {
            $response = $this->makeRequest('GET', '/app/status');

            if ($response->successful()) {
                $data    = $response->json();
                $results = $data['results'] ?? [];

                $isConnected = $results['is_connected'] ?? false;
                $isLoggedIn  = $results['is_logged_in'] ?? false;

                // Map ke status string untuk kompatibilitas view
                $statusStr = match (true) {
                    $isConnected && $isLoggedIn  => 'CONNECTED',
                    $isConnected && !$isLoggedIn => 'SCAN_QR',
                    default                      => 'DISCONNECTED',
                };

                return [
                    'status'       => $statusStr,
                    'is_connected' => $isConnected,
                    'is_logged_in' => $isLoggedIn,
                    'jid'          => $results['jid'] ?? null,
                    'device_id'    => $results['device_id'] ?? $this->deviceId,
                ];
            }

            return ['status' => 'DISCONNECTED', 'device_id' => $this->deviceId];
        } catch (\Exception $e) {
            Log::warning('[GOWA] Tidak bisa cek status: ' . $e->getMessage());
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    /**
     * Trigger QR code login via Go-WA.
     *
     * Go-WA Endpoint: GET /app/login
     * Response: { status: 200, code: "SUCCESS", results: { qr_link: "/..." } }
     *
     * Return URL gambar QR atau null jika tidak tersedia.
     */
    public function getQrCode(): ?string
    {
        try {
            $response = $this->makeRequest('GET', '/app/login');

            if ($response->successful()) {
                $data    = $response->json();
                $qrLink  = $data['results']['qr_link'] ?? null;

                if ($qrLink) {
                    // qr_link biasanya path relatif, jadi gabungkan dengan base URL
                    return $this->baseUrl . '/' . ltrim($qrLink, '/');
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('[GOWA] Tidak bisa ambil QR: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Login via pairing code (alternatif selain QR).
     *
     * Go-WA Endpoint: GET /app/login-with-code?phone=628xxx
     */
    public function loginWithCode(string $phone): ?string
    {
        try {
            $response = $this->makeRequest('GET', '/app/login-with-code', [], [
                'phone' => $this->normalisasiNomor($phone),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['results']['code'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('[GOWA] Tidak bisa login with code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Logout device dari WhatsApp.
     *
     * Go-WA Endpoint: GET /app/logout
     */
    public function logout(): bool
    {
        try {
            $response = $this->makeRequest('GET', '/app/logout');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('[GOWA] Logout error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reconnect device ke WhatsApp.
     *
     * Go-WA Endpoint: GET /app/reconnect
     */
    public function reconnect(): bool
    {
        try {
            $response = $this->makeRequest('GET', '/app/reconnect');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('[GOWA] Reconnect error: ' . $e->getMessage());
            return false;
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
     * Helper untuk HTTP request ke Go-WA dengan Basic Auth opsional.
     *
     * @param string $method   HTTP method (GET/POST)
     * @param string $endpoint API endpoint path
     * @param array  $body     Request body (untuk POST)
     * @param array  $query    Query parameters (untuk GET)
     */
    private function makeRequest(string $method, string $endpoint, array $body = [], array $query = [])
    {
        $request = Http::timeout($this->timeout);

        // Basic Auth (jika username & password diisi)
        if ($this->username && $this->password) {
            $request = $request->withBasicAuth($this->username, $this->password);
        }

        // Device ID header (jika diisi)
        if ($this->deviceId) {
            $request = $request->withHeaders(['X-Device-Id' => $this->deviceId]);
        }

        $url = $this->baseUrl . $endpoint;

        return match (strtoupper($method)) {
            'GET'  => $request->get($url, $query),
            'POST' => $request->post($url, $body),
            default => throw new \InvalidArgumentException("Method {$method} tidak didukung"),
        };
    }
}
