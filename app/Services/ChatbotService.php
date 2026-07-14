<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendWhatsappMessage;
use App\Models\ChatbotLog;
use App\Models\ChatbotSession;
use App\Models\Konfigurasi;
use App\Models\OrangTua;
use App\Models\Pengumuman;
use App\Models\Siswa;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private const TIMEOUT_MINUTES = 30;

    /**
     * Entry point utama — dipanggil dari WhatsappWebhookController.
     */
    public function process(string $noHp, string $pesanMasuk): void
    {
        // 1. Normalisasi nomor HP
        $noHpNormalized = $this->normalisasiNomor($noHp);
        $noHpDb = $this->toLokalFormat($noHpNormalized);

        // 2. Cari orang tua berdasarkan no_wa (bukan no_hp!)
        $orangTuaRef = OrangTua::where('no_wa', $noHpDb)
            ->orWhere('no_wa', $noHpNormalized)
            ->orWhere('no_wa', $noHp)
            ->whereNotNull('no_wa')
            ->first();

        // Fallback: coba cari via no_hp biasa jika no_wa belum diisi
        if (!$orangTuaRef) {
            $orangTuaRef = OrangTua::where('no_hp', $noHpDb)
                ->orWhere('no_hp', $noHpNormalized)
                ->where('is_kontak_utama', true)
                ->first();
        }

        if (!$orangTuaRef) {
            $this->balasDanLog($noHp, $pesanMasuk,
                "Maaf, nomor Anda tidak terdaftar sebagai Wali Siswa di SIAKAD Nurul Jadid Karduluk.\n\nSilakan hubungi admin sekolah untuk mendaftarkan nomor WhatsApp Anda.",
                null, null, 'UNKNOWN_USER'
            );
            return;
        }

        // 3. Ambil semua anak milik wali ini (berdasarkan no_wa yang sama)
        $semua = OrangTua::where('no_wa', $orangTuaRef->no_wa)
            ->whereNotNull('no_wa')
            ->with('siswa')
            ->get();

        // 4. Ambil atau buat sesi chatbot
        $noHpSession = $orangTuaRef->no_wa ?? $noHpDb;
        $session = ChatbotSession::firstOrCreate(
            ['no_hp' => $noHpSession],
            [
                'orang_tua_id' => $orangTuaRef->id,
                'state'        => 'PILIH_ANAK',
                'last_activity' => now(),
            ]
        );

        // 5. Cek timeout
        if (Carbon::parse($session->last_activity)->diffInMinutes(now()) > self::TIMEOUT_MINUTES) {
            $session->state           = $semua->count() > 1 ? 'PILIH_ANAK' : 'MENU_UTAMA';
            $session->anak_terpilih_id = $semua->count() === 1 ? $semua->first()?->siswa?->id : null;
            $session->data_context    = null;
        }

        // 6. Handle keyword global
        $pesanUpper = strtoupper(trim($pesanMasuk));
        if ($pesanUpper === 'MENU') {
            $session->state = 'MENU_UTAMA';
        } elseif (in_array($pesanUpper, ['GANTI ANAK', 'PILIH ANAK']) && $semua->count() > 1) {
            $session->state = 'PILIH_ANAK';
        }

        // 7. Tentukan siswa aktif saat ini
        $siswaAktif = $session->anak_terpilih_id
            ? Siswa::find($session->anak_terpilih_id)
            : null;

        // 8. Routing berdasarkan state
        [$balasan, $newState, $newAnakId, $intent] = $this->routing(
            $session->state, $pesanMasuk, $orangTuaRef, $semua, $siswaAktif
        );

        // 9. Update sesi
        $session->orang_tua_id      = $orangTuaRef->id;
        $session->state             = $newState;
        $session->anak_terpilih_id  = $newAnakId ?? $session->anak_terpilih_id;
        $session->last_activity     = now();
        $session->save();

        // 10. Kirim & log
        $this->balasDanLog($noHp, $pesanMasuk, $balasan, $orangTuaRef->id, $siswaAktif?->id ?? $newAnakId, $intent);
    }

    // ─────────────────────────────────────────────────────────
    // ROUTING STATE MACHINE
    // ─────────────────────────────────────────────────────────

    private function routing(
        string $state,
        string $pesan,
        OrangTua $orangTuaRef,
        Collection $semua,
        ?Siswa $siswaAktif
    ): array {
        $input = trim($pesan);

        return match ($state) {
            'PILIH_ANAK' => $this->handlePilihAnak($input, $semua),
            'MENU_UTAMA' => $this->handleMenuUtama($input, $orangTuaRef, $siswaAktif, $semua),
            default      => [
                $this->getMenuUtamaText($orangTuaRef->nama, $siswaAktif, $semua->count() > 1),
                'MENU_UTAMA',
                $siswaAktif?->id,
                'RESET_STATE',
            ],
        };
    }

    private function handlePilihAnak(string $input, Collection $semua): array
    {
        $anak = $semua->values(); // re-index

        // Jika hanya 1 anak, langsung ke MENU_UTAMA
        if ($anak->count() === 1) {
            $siswa = $anak->first()->siswa;
            $wali  = $anak->first();
            return [
                $this->getMenuUtamaText($wali->nama, $siswa, false),
                'MENU_UTAMA',
                $siswa?->id,
                'AUTO_SELECT_CHILD',
            ];
        }

        // Cek apakah input adalah angka valid
        $idx = (int) $input - 1;
        if (is_numeric($input) && $idx >= 0 && $idx < $anak->count()) {
            $pilihan = $anak[$idx];
            $siswa   = $pilihan->siswa;
            $wali    = $pilihan;

            return [
                "✅ Anda memilih *{$siswa->nama_lengkap}* — Kelas {$siswa->kelas?->nama_kelas}\n\n" .
                $this->getMenuUtamaText($wali->nama, $siswa, true),
                'MENU_UTAMA',
                $siswa->id,
                'PILIH_ANAK',
            ];
        }

        // Tampilkan menu pilih anak
        return [$this->getPilihAnakText($anak), 'PILIH_ANAK', null, 'SHOW_PILIH_ANAK'];
    }

    private function handleMenuUtama(
        string $input,
        OrangTua $orangTuaRef,
        ?Siswa $siswaAktif,
        Collection $semua
    ): array {
        if (!in_array($input, ['1', '2', '3', '4', '5'])) {
            return [
                $this->getMenuUtamaText($orangTuaRef->nama, $siswaAktif, $semua->count() > 1),
                'MENU_UTAMA',
                $siswaAktif?->id,
                'SHOW_MENU',
            ];
        }

        $balasan = match ($input) {
            '1' => $this->getInfoNilai($siswaAktif),
            '2' => $this->getInfoKehadiran($siswaAktif),
            '3' => $this->getInfoTagihan($siswaAktif),
            '4' => $this->getInfoAgenda(),
            '5' => $this->getCsInfo(),
        };

        $intent = match ($input) {
            '1' => 'INFO_NILAI',
            '2' => 'INFO_KEHADIRAN',
            '3' => 'INFO_TAGIHAN',
            '4' => 'INFO_AGENDA',
            '5' => 'CS_CONTACT',
        };

        $footer = "\n\nKetik 'MENU' untuk kembali ke menu.";
        if ($semua->count() > 1) {
            $footer .= "\nKetik 'GANTI ANAK' untuk mengganti pilihan anak.";
        }

        return [$balasan . $footer, 'MENU_UTAMA', $siswaAktif?->id, $intent];
    }

    // ─────────────────────────────────────────────────────────
    // TEMPLATE TEKS
    // ─────────────────────────────────────────────────────────

    private function getPilihAnakText(Collection $anak): string
    {
        $text = "👨‍👩‍👧‍👦 *Anda memiliki {$anak->count()} anak terdaftar:*\n\n";
        foreach ($anak as $i => $a) {
            $kelas = $a->siswa?->kelas?->nama_kelas ?? '—';
            $text .= "[" . ($i + 1) . "] *{$a->siswa?->nama_lengkap}* — Kelas {$kelas}\n";
        }
        $text .= "\nKetik nomor untuk memilih anak.";
        return $text;
    }

    private function getMenuUtamaText(string $nama, ?Siswa $siswa, bool $punya_banyak_anak): string
    {
        $header = "🏫 *SIAKAD Nurul Jadid Karduluk*\n";
        $header .= "Selamat datang, *{$nama}*.";
        if ($siswa) {
            $kelas = $siswa->kelas?->nama_kelas ?? '—';
            $header .= "\nAnanda: *{$siswa->nama_lengkap}* (Kelas {$kelas})";
        }

        $menu = "\n\nKetik angka layanan:\n"
            . "[1] 📊 Info Nilai Rapor\n"
            . "[2] 📋 Info Rekap Kehadiran\n"
            . "[3] 💳 Info Tagihan & Pembayaran\n"
            . "[4] 📢 Info Agenda Sekolah Terbaru\n"
            . "[5] 📞 Hubungi Customer Service";

        if ($punya_banyak_anak) {
            $menu .= "\n\nKetik 'GANTI ANAK' untuk mengganti pilihan anak.";
        }

        return $header . $menu;
    }

    // ─────────────────────────────────────────────────────────
    // QUERY DATA
    // ─────────────────────────────────────────────────────────

    private function getInfoNilai(?Siswa $siswa): string
    {
        if (!$siswa) {
            return "⚠️ Data siswa belum dipilih. Ketik 'GANTI ANAK' untuk memilih.";
        }

        $nilai = $siswa->nilai()->with('mapel')->get();
        if ($nilai->isEmpty()) {
            return "Belum ada data nilai untuk Ananda *{$siswa->nama_lengkap}*.";
        }

        $total = 0;
        $count = $nilai->count();
        $text  = "📊 *Nilai Ananda {$siswa->nama_lengkap}*\n";

        foreach ($nilai as $n) {
            $total += (float) $n->nilai_akhir;
            $status = ((float) $n->nilai_akhir) >= 75 ? 'Tuntas' : 'Remedial';
            $text  .= "• {$n->mapel->nama_mapel}: *{$n->nilai_akhir}* ({$status})\n";
        }

        $rata  = round($total / $count, 1);
        $text .= "\nRata-rata: *{$rata}*";
        return $text;
    }

    private function getInfoKehadiran(?Siswa $siswa): string
    {
        if (!$siswa) {
            return "⚠️ Data siswa belum dipilih. Ketik 'GANTI ANAK' untuk memilih.";
        }

        $absensi = $siswa->absensi()->get();
        $hadir   = $absensi->where('status', 'Hadir')->count();
        $sakit   = $absensi->where('status', 'Sakit')->count();
        $izin    = $absensi->where('status', 'Izin')->count();
        $alpa    = $absensi->where('status', 'Alpa')->count();
        $total   = $absensi->count();
        $pctHadir = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

        return "📋 *Rekap Kehadiran Ananda {$siswa->nama_lengkap}*\n\n"
            . "• Hadir : *{$hadir}x*\n"
            . "• Sakit : {$sakit}x\n"
            . "• Izin  : {$izin}x\n"
            . "• Alpa  : *{$alpa}x*\n"
            . "\nPersentase Kehadiran: *{$pctHadir}%*";
    }

    private function getInfoTagihan(?Siswa $siswa): string
    {
        if (!$siswa) {
            return "⚠️ Data siswa belum dipilih. Ketik 'GANTI ANAK' untuk memilih.";
        }

        $tagihan = Tagihan::where('siswa_id', $siswa->id)
            ->where('status_lunas', false)
            ->get();

        if ($tagihan->isEmpty()) {
            return "✅ *Info Tagihan Ananda {$siswa->nama_lengkap}*\n\nAlhamdulillah, tidak ada tunggakan. Terima kasih atas pembayarannya! 🙏";
        }

        $text  = "💳 *Info Tagihan Ananda {$siswa->nama_lengkap}*\n\nTagihan yang belum lunas:\n";
        $total = 0;
        foreach ($tagihan as $t) {
            $text  .= "• {$t->nama_tagihan}: *Rp " . number_format($t->nominal, 0, ',', '.') . "*\n";
            $total += $t->nominal;
        }
        $text .= "\n*Total: Rp " . number_format($total, 0, ',', '.') . "*";
        return $text;
    }

    private function getInfoAgenda(): string
    {
        $pengumuman = Pengumuman::orderBy('created_at', 'desc')->take(3)->get();

        if ($pengumuman->isEmpty()) {
            return "Belum ada agenda atau pengumuman sekolah terbaru.";
        }

        $text = "📢 *Agenda & Pengumuman Sekolah*\n\n";
        foreach ($pengumuman as $p) {
            $tgl   = Carbon::parse($p->created_at)->translatedFormat('d M Y');
            $isi   = mb_substr($p->isi, 0, 150) . (mb_strlen($p->isi) > 150 ? '...' : '');
            $text .= "🗓️ *{$p->judul}* ({$tgl})\n{$isi}\n\n";
        }
        return rtrim($text);
    }

    private function getCsInfo(): string
    {
        $cs = Konfigurasi::get('cs_whatsapp', '08123456789');
        return "📞 *Layanan Customer Service*\n\nJika ada kendala atau pertanyaan, silakan hubungi admin melalui:\nwa.me/{$cs}";
    }

    // ─────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────

    private function normalisasiNomor(string $noHp): string
    {
        $noHp = preg_replace('/[^0-9]/', '', $noHp);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }
        // Strip @c.us jika ada (dari format WAHA)
        return preg_replace('/@.*$/', '', $noHp);
    }

    private function toLokalFormat(string $noHp): string
    {
        if (str_starts_with($noHp, '62')) {
            return '0' . substr($noHp, 2);
        }
        return $noHp;
    }

    private function balasDanLog(
        string $noHp,
        string $pesanMasuk,
        string $balasan,
        ?int $orangTuaId,
        ?int $siswaId,
        string $intent
    ): void {
        // Dispatch Job async
        SendWhatsappMessage::dispatch($noHp, $balasan);

        // Log percakapan
        ChatbotLog::create([
            'no_hp'       => $this->toLokalFormat($this->normalisasiNomor($noHp)),
            'pesan_masuk' => $pesanMasuk,
            'pesan_keluar'=> $balasan,
            'siswa_id'    => $siswaId,
            'intent'      => $intent,
        ]);
    }
}
