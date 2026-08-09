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

        // Filter penerima: Ambil kontak utama dari setiap siswa (atau per kelas jika diset)
        $siswaQuery = \App\Models\Siswa::query();
        if ($pengumuman->kelas_id) {
            $siswaQuery->where('kelas_id', $pengumuman->kelas_id);
        }
        $siswaList = $siswaQuery->get();

        $sentWaliIds = [];
        foreach ($siswaList as $siswa) {
            $wali = $siswa->getKontakUtamaWali();
            if ($wali && $wali->no_wa && ! in_array($wali->id, $sentWaliIds, true)) {
                $sentWaliIds[] = $wali->id;
                SendWhatsappMessage::dispatch($wali->no_wa, $pesan, 'pengumuman', $wali->id, $siswa->id);
            }
        }
    }
}
