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
     * Dipicu saat pengumuman di-publish.
     */
    public function updated(Pengumuman $pengumuman): void
    {
        if ($pengumuman->is_active && $pengumuman->wasChanged('is_active')) {
            $this->broadcast($pengumuman);
        }
    }

    public function created(Pengumuman $pengumuman): void
    {
        if ($pengumuman->is_active) {
            $this->broadcast($pengumuman);
        }
    }

    private function broadcast(Pengumuman $pengumuman): void
    {
        $header = "📢 *Pengumuman Sekolah*";
        if ($pengumuman->kelas_id && $pengumuman->kelas) {
            $header .= " (Khusus " . $pengumuman->kelas->nama_kelas . ")";
        }

        $template = Konfigurasi::get(
            'template_pengumuman',
            "{$header}\n*{judul}*\n\n{isi}\n\n— SIAKAD Nurul Jadid Karduluk"
        );

        $isiRaw = (string) ($pengumuman->konten ?? $pengumuman->isi ?? '');
        $isi    = mb_substr($isiRaw, 0, 500) . (mb_strlen($isiRaw) > 500 ? '...' : '');
        $pesan  = strtr($template, [
            '{judul}'   => $pengumuman->judul,
            '{isi}'     => $isi,
            '{tanggal}' => Carbon::parse($pengumuman->tanggal_publish ?? $pengumuman->created_at ?? now())->translatedFormat('d F Y'),
        ]);

        // Filter penerima: Jika ada kelas_id, filter hanya wali dari siswa di kelas tersebut
        $query = OrangTua::where('is_kontak_utama', true)->whereNotNull('no_wa');

        if ($pengumuman->kelas_id) {
            $query->whereHas('siswa', function ($q) use ($pengumuman): void {
                $q->where('kelas_id', $pengumuman->kelas_id);
            });
        }

        $waliList = $query->get();

        foreach ($waliList as $wali) {
            SendWhatsappMessage::dispatch($wali->no_wa, $pesan, 'pengumuman', $wali->id, $wali->siswa_id);
        }
    }
}
