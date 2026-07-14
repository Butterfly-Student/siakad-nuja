<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SendWhatsappMessage;
use App\Models\Absensi;
use App\Models\Konfigurasi;
use App\Models\OrangTua;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AbsensiObserver
{
    /**
     * Dipicu setiap kali data absensi baru disimpan.
     * Notifikasi hanya dikirim jika status bukan 'Hadir'.
     */
    public function created(Absensi $absensi): void
    {
        // Hanya kirim notif untuk ketidakhadiran
        if ($absensi->status === 'Hadir') {
            return;
        }

        $siswa = $absensi->siswa;
        if (!$siswa) {
            return;
        }

        // Ambil hanya kontak utama yang punya nomor WA
        $wali = OrangTua::where('siswa_id', $siswa->id)
            ->where('is_kontak_utama', true)
            ->whereNotNull('no_wa')
            ->first();

        if (!$wali) {
            Log::info("[Notif WA] Absensi siswa ID {$siswa->id}: tidak ada wali dengan no_wa.");
            return;
        }

        // Render template
        $template = Konfigurasi::get(
            'template_absensi',
            "🔔 *Notifikasi Kehadiran*\nYth. Bpk/Ibu {nama_wali},\n\nAnanda *{nama_siswa}* ({kelas}) tercatat *{status}* pada hari {hari}, {tanggal}.\n\n— SIAKAD Nurul Jadid Karduluk"
        );

        $pesan = strtr($template, [
            '{nama_wali}'  => $wali->nama,
            '{nama_siswa}' => $siswa->nama_lengkap,
            '{kelas}'      => $siswa->kelas?->nama_kelas ?? '—',
            '{status}'     => $absensi->status,
            '{hari}'       => Carbon::parse($absensi->tanggal)->translatedFormat('l'),
            '{tanggal}'    => Carbon::parse($absensi->tanggal)->translatedFormat('d F Y'),
            '{keterangan}' => $absensi->keterangan ?? '',
        ]);

        SendWhatsappMessage::dispatch($wali->no_wa, $pesan, 'absensi', $wali->id, $siswa->id);
    }
}
