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
            \Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::send($this->normalisasiNomor($noHp), $pesan);
            Log::info("[LaravelWhatsApp] Berhasil kirim ke {$noHp}");
            return true;
        } catch (\Exception $e) {
            Log::error("[LaravelWhatsApp] Exception kirim ke {$noHp}: " . $e->getMessage());
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
            'status'        => $success ? 'terkirim' : 'gagal',
            'dikirim_pada'  => $success ? now() : null,
            'error_message' => $success ? null : 'Gagal terkirim (Nomor tidak terdaftar di WhatsApp atau gateway offline)',
        ]);

        return $success;
    }

    /**
     * Cek status koneksi laravel-whatsapp.
     */
    public function getStatus(): array
    {
        try {
            if (class_exists(\Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::class)) {
                $sessionState = \Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::web('main')->state();
                $state        = strtolower($sessionState['status'] ?? 'disconnected');

                $statusStr = match ($state) {
                    'ready', 'authenticated' => 'CONNECTED',
                    'qr'                     => 'SCAN_QR',
                    default                  => 'DISCONNECTED',
                };

                return [
                    'status'       => $statusStr,
                    'is_connected' => in_array($state, ['ready', 'authenticated'], true),
                    'is_logged_in' => in_array($state, ['ready', 'authenticated'], true),
                    'jid'          => $sessionState['id'] ?? 'main',
                    'device_id'    => 'laravel-whatsapp-sidecar',
                ];
            }

            return ['status' => 'DISCONNECTED', 'device_id' => 'laravel-whatsapp-sidecar'];
        } catch (\Exception $e) {
            Log::warning('[LaravelWhatsApp] tidak bisa cek status sidecar: ' . $e->getMessage());
            return ['status' => 'DISCONNECTED', 'message' => $e->getMessage()];
        }
    }

    /**
     * Trigger QR code login via laravel-whatsapp sidecar.
     */
    public function getQrCode(): ?string
    {
        try {
            if (class_exists(\Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::class)) {
                $qrData = \Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::web('main')->qr();
                return $qrData['qr'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('[LaravelWhatsApp] Tidak bisa ambil QR: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Login via pairing code.
     */
    public function loginWithCode(string $phone): ?string
    {
        return null;
    }

    /**
     * Logout device dari WhatsApp via laravel-whatsapp.
     */
    public function logout(): bool
    {
        try {
            if (class_exists(\Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::class)) {
                \Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::web('main')->stop();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('[LaravelWhatsApp] Logout error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reconnect device ke WhatsApp via laravel-whatsapp.
     */
    public function reconnect(): bool
    {
        try {
            if (class_exists(\Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::class)) {
                \Kstmostofa\LaravelWhatsApp\Facades\WhatsApp::web('main')->start();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('[LaravelWhatsApp] Reconnect error: ' . $e->getMessage());
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
