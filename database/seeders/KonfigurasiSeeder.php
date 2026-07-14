<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Konfigurasi;
use Illuminate\Database\Seeder;

class KonfigurasiSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'cs_whatsapp' => '6281234567890',

            'template_absensi' =>
                "🔔 *Notifikasi Kehadiran*\n" .
                "Yth. Bpk/Ibu {nama_wali},\n\n" .
                "Ananda *{nama_siswa}* ({kelas}) tercatat *{status}*\n" .
                "pada hari {hari}, {tanggal}.\n\n" .
                "Harap menghubungi sekolah jika ada keterangan.\n\n" .
                "— SIAKAD Nurul Jadid Karduluk",

            'template_nilai' =>
                "📊 *Notifikasi Nilai Baru*\n" .
                "Yth. Bpk/Ibu {nama_wali},\n\n" .
                "Nilai *{mapel}* Ananda *{nama_siswa}* ({kelas}) telah diinput:\n" .
                "• Tugas  : {nilai_harian}\n" .
                "• UTS    : {nilai_uts}\n" .
                "• UAS    : {nilai_uas}\n" .
                "• *Nilai Akhir : {nilai_akhir} ({predikat})*\n\n" .
                "— SIAKAD Nurul Jadid Karduluk",

            'template_tagihan' =>
                "💳 *Notifikasi Tagihan*\n" .
                "Yth. Bpk/Ibu {nama_wali},\n\n" .
                "Tagihan baru untuk Ananda *{nama_siswa}*:\n" .
                "• Nama   : {nama_tagihan}\n" .
                "• Nominal: Rp {nominal}\n\n" .
                "Mohon segera melakukan pembayaran.\n\n" .
                "— SIAKAD Nurul Jadid Karduluk",

            'template_pengumuman' =>
                "📢 *Pengumuman Sekolah*\n" .
                "*{judul}*\n\n" .
                "{isi}\n\n" .
                "— SIAKAD Nurul Jadid Karduluk",

            'template_kuitansi' =>
                "✅ *Konfirmasi Pembayaran*\n" .
                "Yth. Bpk/Ibu {nama_wali},\n\n" .
                "Pembayaran *{nama_tagihan}* untuk Ananda *{nama_siswa}* telah *DIVERIFIKASI*.\n" .
                "Nominal: Rp {nominal}\n" .
                "Tanggal: {tanggal}\n\n" .
                "Terima kasih atas pembayarannya 🙏\n\n" .
                "— SIAKAD Nurul Jadid Karduluk",
        ];

        foreach ($defaults as $key => $value) {
            Konfigurasi::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
