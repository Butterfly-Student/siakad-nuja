<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SendWhatsappMessage;
use App\Models\Konfigurasi;
use App\Models\Nilai;
use App\Models\OrangTua;
use Illuminate\Support\Facades\Log;

class NilaiObserver
{
    /**
     * Dipicu ketika nilai baru pertama kali dibuat.
     */
    public function created(Nilai $nilai): void
    {
        $siswa = $nilai->siswa;
        if (!$siswa) {
            return;
        }

        $wali = OrangTua::where('siswa_id', $siswa->id)
            ->where('is_kontak_utama', true)
            ->whereNotNull('no_wa')
            ->first();

        if (!$wali) {
            return;
        }

        $mapel    = $nilai->mapel;
        $template = Konfigurasi::get(
            'template_nilai',
            "📊 *Notifikasi Nilai Baru*\nYth. Bpk/Ibu {nama_wali},\n\nNilai *{mapel}* Ananda *{nama_siswa}* telah diinput:\n• Tugas  : {nilai_harian}\n• UTS    : {nilai_uts}\n• UAS    : {nilai_uas}\n• *Nilai Akhir : {nilai_akhir} ({predikat})*\n\n— SIAKAD Nurul Jadid Karduluk"
        );

        $pesan = strtr($template, [
            '{nama_wali}'   => $wali->nama,
            '{nama_siswa}'  => $siswa->nama_lengkap,
            '{kelas}'       => $siswa->kelas?->nama_kelas ?? '—',
            '{mapel}'       => $mapel?->nama_mapel ?? '—',
            '{nilai_harian}'=> $nilai->nilai_harian ?? '—',
            '{nilai_uts}'   => $nilai->nilai_uts ?? '—',
            '{nilai_uas}'   => $nilai->nilai_uas ?? '—',
            '{nilai_akhir}' => $nilai->nilai_akhir ?? '—',
            '{predikat}'    => $nilai->predikat ?? '—',
        ]);

        SendWhatsappMessage::dispatch($wali->no_wa, $pesan, 'nilai', $wali->id, $siswa->id);
    }
}
