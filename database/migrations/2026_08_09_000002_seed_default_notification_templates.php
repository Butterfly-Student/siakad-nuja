<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use App\Models\Konfigurasi;

return new class extends Migration
{
    public function up(): void
    {
        $templates = [
            'template_absensi' => "🔔 *Notifikasi Kehadiran*\nYth. Bpk/Ibu {nama_wali},\n\nAnanda *{nama_siswa}* ({kelas}) tercatat *{status}* pada hari {hari}, {tanggal}.\n\n— SIAKAD Nurul Jadid Karduluk",
            'template_nilai' => "📊 *Notifikasi Nilai Baru*\nYth. Bpk/Ibu {nama_wali},\n\nNilai *{mapel}* Ananda *{nama_siswa}* telah diinput:\n• Tugas  : {nilai_harian}\n• UTS    : {nilai_uts}\n• UAS    : {nilai_uas}\n• *Nilai Akhir : {nilai_akhir} ({predikat})*\n\n— SIAKAD Nurul Jadid Karduluk",
            'template_tagihan' => "💳 *Notifikasi Tagihan*\nYth. Bpk/Ibu {nama_wali},\n\nTagihan baru untuk Ananda *{nama_siswa}*:\n• Nama   : {nama_tagihan}\n• Nominal: Rp {nominal}\n\nMohon untuk segera melakukan pembayaran.\n\n— SIAKAD Nurul Jadid Karduluk",
            'template_pengumuman' => "📢 *Pengumuman Sekolah*\n*{judul}*\n\n{isi}\n\n— SIAKAD Nurul Jadid Karduluk",
            'template_kuitansi' => "✅ *Pembayaran Berhasil*\nYth. Bpk/Ibu {nama_wali},\n\nPembayaran *{nama_tagihan}* Ananda *{nama_siswa}* sebesar *Rp {nominal}* pada {tanggal_bayar} telah dikonfirmasi LUNAS.\n\nTerima kasih! 🙏\n\n— SIAKAD Nurul Jadid Karduluk",
            'template_teguran' => "⚠️ *Pemberitahuan Teguran Sekolah*\nYth. Bpk/Ibu {nama_wali},\n\nDisampaikan mengenai Ananda *{nama_siswa}*:\n{keterangan}\n\nTanggal: {tanggal}\n\nMohon kerjasamanya untuk perhatian Bpk/Ibu.\n\n— SIAKAD Nurul Jadid Karduluk",
            'cs_whatsapp' => '081234567890',
        ];

        foreach ($templates as $key => $value) {
            Konfigurasi::firstOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function down(): void
    {
        // No destructive rollback needed
    }
};
