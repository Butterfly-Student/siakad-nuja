<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotifikasiWhatsapp;
use Illuminate\Support\Facades\Log;
use Kstmostofa\LaravelWhatsApp\Facades\WhatsApp;

class WhatsappGatewayService
{
    private string $phoneSuffix;
    private int $timeout;

    public function __construct()
    {
        $this->phoneSuffix = (string) config('whatsapp.phone_suffix', '@s.whatsapp.net');
        $this->timeout     = (int) config('whatsapp.timeout', 30);
    }

    /**
     * Normalisasi nomor HP ke format internasional 628xxx (tanpa tanda +).
     */
    public function normalisasiNomor(string $noHp): string
    {
        $noHp = preg_replace('/[^0-9]/', '', $noHp);

        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        return $noHp;
    }

    /**
     * Kirim pesan teks via laravel-whatsapp facade.
     */
    public function send(string $noHp, string $pesan): bool
    {
        try {
            WhatsApp::send($this->normalisasiNomor($noHp), $pesan);
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
        $log = NotifikasiWhatsapp::create([
            'orang_tua_id' => $orangTuaId,
            'siswa_id'     => $siswaId,
            'no_tujuan'    => $this->normalisasiNomor($noHp),
            'jenis'        => $jenis,
            'isi_pesan'    => $pesan,
            'status'       => 'pending',
        ]);

        $success = $this->send($noHp, $pesan);

        $log->update([
            'status'        => $success ? 'terkirim' : 'gagal',
            'dikirim_pada'  => $success ? now() : null,
            'error_message' => $success ? null : 'Gagal terkirim (Nomor tidak terdaftar di WhatsApp atau gateway offline)',
        ]);

        return $success;
    }

    /**
     * Cek status koneksi laravel-whatsapp sidecar.
     */
    public function getStatus(): array
    {
        try {
            $sessionState = WhatsApp::web('main')->state();
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
            $qrData = WhatsApp::web('main')->qr();
            return $qrData['qr'] ?? null;
        } catch (\Exception $e) {
            Log::warning('[LaravelWhatsApp] Tidak bisa ambil QR: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Logout device dari WhatsApp via laravel-whatsapp.
     */
    public function logout(): bool
    {
        try {
            WhatsApp::web('main')->stop();
            return true;
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
            WhatsApp::web('main')->start();
            return true;
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
}
