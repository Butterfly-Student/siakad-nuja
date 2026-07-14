<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SendWhatsappMessage;
use App\Models\Konfigurasi;
use App\Models\OrangTua;
use App\Models\Pengumuman;
use Carbon\Carbon;

class PengumumanObserver
{
    /**
     * Dipicu saat pengumuman di-publish, jika kirim_wa = true.
     */
    public function updated(Pengumuman $pengumuman): void
    {
        // Hanya kirim WA jika status baru saja diubah ke 'publish' dan kirim_wa = true
        if (!$pengumuman->wasChanged('status')) {
            return;
        }

        if ($pengumuman->status !== 'publish' || !$pengumuman->kirim_wa) {
            return;
        }

        $this->broadcast($pengumuman);
    }

    public function created(Pengumuman $pengumuman): void
    {
        if ($pengumuman->status === 'publish' && $pengumuman->kirim_wa) {
            $this->broadcast($pengumuman);
        }
    }

    private function broadcast(Pengumuman $pengumuman): void
    {
        $template = Konfigurasi::get(
            'template_pengumuman',
            "📢 *Pengumuman Sekolah*\n*{judul}*\n\n{isi}\n\n— SIAKAD Nurul Jadid Karduluk"
        );

        $isi   = mb_substr($pengumuman->isi, 0, 500) . (mb_strlen($pengumuman->isi) > 500 ? '...' : '');
        $pesan = strtr($template, [
            '{judul}'   => $pengumuman->judul,
            '{isi}'     => $isi,
            '{tanggal}' => Carbon::parse($pengumuman->tanggal_publikasi ?? now())->translatedFormat('d F Y'),
        ]);

        // Kirim ke semua orang tua (is_kontak_utama) yang punya no_wa
        $waliList = OrangTua::where('is_kontak_utama', true)
            ->whereNotNull('no_wa')
            ->get();

        foreach ($waliList as $wali) {
            SendWhatsappMessage::dispatch($wali->no_wa, $pesan, 'pengumuman', $wali->id, $wali->siswa_id);
        }
    }
}
