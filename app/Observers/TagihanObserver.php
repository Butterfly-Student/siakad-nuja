<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SendWhatsappMessage;
use App\Models\Konfigurasi;
use App\Models\OrangTua;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;

class TagihanObserver
{
    /**
     * Dipicu saat tagihan baru diterbitkan.
     */
    public function created(Tagihan $tagihan): void
    {
        $siswa = $tagihan->siswa;
        if (!$siswa) {
            return;
        }

        $wali = $siswa->getKontakUtamaWali();

        if (!$wali || !$wali->no_wa) {
            return;
        }

        $template = Konfigurasi::get(
            'template_tagihan',
            "💳 *Notifikasi Tagihan*\nYth. Bpk/Ibu {nama_wali},\n\nTagihan baru untuk Ananda *{nama_siswa}*:\n• Nama   : {nama_tagihan}\n• Nominal: Rp {nominal}\n\nMohon untuk segera melakukan pembayaran.\n\n— SIAKAD Nurul Jadid Karduluk"
        );

        $pesan = strtr($template, [
            '{nama_wali}'    => $wali->nama,
            '{nama_siswa}'   => $siswa->nama_lengkap,
            '{nama_tagihan}' => $tagihan->judul ?? $tagihan->nama_tagihan ?? $tagihan->jenis ?? 'Tagihan',
            '{nominal}'      => number_format((float) $tagihan->nominal, 0, ',', '.'),
        ]);

        SendWhatsappMessage::dispatch($wali->no_wa, $pesan, 'tagihan', $wali->id, $siswa->id);
    }
}
