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
    public function process(string $noHp, string $pesanMasuk, ?string $senderNumber = null): void
    {
        // 1. Normalisasi nomor HP yang masuk & senderNumber jika ada
        $cleanNoHp   = preg_replace('/[^0-9]/', '', $noHp);
        $cleanSender = $senderNumber ? preg_replace('/[^0-9]/', '', $senderNumber) : null;

        $targetNumbers = array_unique(array_filter([
            $cleanNoHp,
            $cleanSender,
            str_starts_with((string)$cleanSender, '0') ? ('62' . substr($cleanSender, 1)) : $cleanSender,
            str_starts_with((string)$cleanSender, '62') ? ('0' . substr($cleanSender, 2)) : $cleanSender,
            str_starts_with($cleanNoHp, '0') ? ('62' . substr($cleanNoHp, 1)) : $cleanNoHp,
            str_starts_with($cleanNoHp, '62') ? ('0' . substr($cleanNoHp, 2)) : $cleanNoHp,
            $noHp,
        ]));

        // 2. Cari orang tua berdasarkan pencocokan nomor WA / HP yang valid
        $orangTuaRef = OrangTua::all()->first(function ($ot) use ($targetNumbers) {
            $waClean = preg_replace('/[^0-9]/', '', (string) $ot->no_wa);
            $hpClean = preg_replace('/[^0-9]/', '', (string) $ot->no_hp);

            return (! empty($waClean) && in_array($waClean, $targetNumbers, true))
                || (! empty($hpClean) && in_array($hpClean, $targetNumbers, true));
        });

        // Jika nomor belum terdaftar sebagai wali
        if (! $orangTuaRef) {
            $rule = \App\Models\ChatbotRule::where('is_active', true)
                ->where(function ($q) use ($pesanMasuk): void {
                    $q->where('keyword', trim($pesanMasuk))
                      ->orWhere('keyword', strtoupper(trim($pesanMasuk)));
                })
                ->first();

            if ($rule) {
                $balasan = ($rule->tipe_action === 'system_query')
                    ? $this->getInfoAgenda()
                    : ($rule->isi_balasan ?? 'Informasi tidak tersedia.');
            } else {
                $balasan = "🏫 *SIAKAD Nurul Jadid Karduluk*\n\nSelamat datang. Nomor Anda belum terdaftar sebagai Wali Siswa.\n\nJika Anda adalah Wali Siswa, silakan hubungi admin sekolah untuk mendaftarkan nomor WhatsApp Anda.";
            }

            $displayNum = $cleanSender ? $this->toLokalFormat($cleanSender) : $this->toLokalFormat($cleanNoHp);
            $this->balasDanLog($noHp, $pesanMasuk, $balasan, null, null, 'GUEST_USER', $displayNum);
            return;
        }

        // 3. Ambil semua anak milik wali ini (berdasarkan no_wa / no_hp yang sama)
        $semua = OrangTua::where(function ($q) use ($orangTuaRef): void {
            if ($orangTuaRef->no_wa) {
                $q->where('no_wa', $orangTuaRef->no_wa);
            }
            if ($orangTuaRef->no_hp) {
                $q->orWhere('no_hp', $orangTuaRef->no_hp);
            }
        })
        ->whereNotNull('siswa_id')
        ->with('siswa')
        ->get();

        if ($semua->isEmpty()) {
            $semua = collect([$orangTuaRef]);
        }

        $singleSiswaId = ($semua->count() === 1)
            ? ($semua->first()?->siswa_id ?? $semua->first()?->siswa?->id)
            : null;

        // 4. Ambil atau buat sesi chatbot
        $noHpSession = $orangTuaRef->no_wa ?? $orangTuaRef->no_hp ?? $noHpDb;
        $session = ChatbotSession::firstOrCreate(
            ['no_hp' => $noHpSession],
            [
                'orang_tua_id'     => $orangTuaRef->id,
                'state'            => $semua->count() > 1 ? 'PILIH_ANAK' : 'MENU_UTAMA',
                'anak_terpilih_id' => $singleSiswaId,
                'last_activity'    => now(),
            ]
        );

        // 5. Cek timeout (30 menit) -> reset
        if (Carbon::parse($session->last_activity)->diffInMinutes(now()) > self::TIMEOUT_MINUTES) {
            $session->state            = $semua->count() > 1 ? 'PILIH_ANAK' : 'MENU_UTAMA';
            $session->anak_terpilih_id = $singleSiswaId;
            $session->data_context     = null;
        }

        // 6. Jika hanya 1 anak, otomatis pilih anak tersebut dan pastikan state MENU_UTAMA
        if ($semua->count() === 1) {
            $session->anak_terpilih_id = $singleSiswaId;
            if ($session->state === 'PILIH_ANAK') {
                $session->state = 'MENU_UTAMA';
            }
        }

        // 7. Handle keyword global
        $pesanUpper = strtoupper(trim($pesanMasuk));
        if ($pesanUpper === 'MENU') {
            $session->state = ($semua->count() > 1 && !$session->anak_terpilih_id) ? 'PILIH_ANAK' : 'MENU_UTAMA';
        } elseif (in_array($pesanUpper, ['GANTI ANAK', 'PILIH ANAK']) && $semua->count() > 1) {
            $session->state = 'PILIH_ANAK';
            $session->anak_terpilih_id = null;
        }

        // 8. Tentukan siswa aktif saat ini
        $siswaAktif = $session->anak_terpilih_id
            ? Siswa::find($session->anak_terpilih_id)
            : null;

        // 9. Routing berdasarkan state
        [$balasan, $newState, $newAnakId, $intent] = $this->routing(
            $session->state, $pesanMasuk, $orangTuaRef, $semua, $siswaAktif
        );

        // 10. Update sesi
        $session->orang_tua_id      = $orangTuaRef->id;
        $session->state             = $newState;
        $session->anak_terpilih_id  = $newAnakId ?? $session->anak_terpilih_id;
        $session->last_activity     = now();
        $session->save();

        // 11. Kirim & log
        $displayNum = $this->toLokalFormat($orangTuaRef->no_wa ?? $orangTuaRef->no_hp ?? $cleanSender ?? $cleanNoHp);
        $this->balasDanLog($noHp, $pesanMasuk, $balasan, $orangTuaRef->id, $siswaAktif?->id ?? $newAnakId, $intent, $displayNum);
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
        $rule = \App\Models\ChatbotRule::where('is_active', true)
            ->where(function ($q) use ($input): void {
                $q->where('keyword', $input)
                  ->orWhere('keyword', strtoupper($input));
            })
            ->first();

        if (! $rule) {
            return [
                $this->getMenuUtamaText($orangTuaRef->nama, $siswaAktif, $semua->count() > 1),
                'MENU_UTAMA',
                $siswaAktif?->id,
                'SHOW_MENU',
            ];
        }

        if ($rule->tipe_action === 'system_query') {
            $balasan = match ($rule->action_key) {
                'info_nilai'     => $this->getInfoNilai($siswaAktif),
                'info_kehadiran' => $this->getInfoKehadiran($siswaAktif),
                'info_tagihan'   => $this->getInfoTagihan($siswaAktif),
                'info_agenda'    => $this->getInfoAgenda(),
                'cs_contact'     => $this->getCsInfo(),
                default          => 'Informasi tidak tersedia.',
            };
            $intent = strtoupper($rule->action_key ?? 'SYSTEM_QUERY');
        } else {
            $balasan = $this->parseTemplate($rule->isi_balasan ?? '', $orangTuaRef, $siswaAktif);
            $intent  = 'CUSTOM_RULE_' . strtoupper($rule->keyword);
        }

        $footer = "\n\nKetik 'MENU' untuk kembali ke menu.";
        if ($semua->count() > 1) {
            $footer .= "\nKetik 'GANTI ANAK' untuk mengganti pilihan anak.";
        }

        return [$balasan . $footer, 'MENU_UTAMA', $siswaAktif?->id, $intent];
    }

    private function parseTemplate(string $template, OrangTua $orangTua, ?Siswa $siswa): string
    {
        return strtr($template, [
            '{nama_wali}'  => $orangTua->nama,
            '{nama_siswa}' => $siswa?->nama_lengkap ?? 'Ananda',
            '{kelas}'      => $siswa?->kelas?->nama_kelas ?? '—',
        ]);
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

        $rules = \App\Models\ChatbotRule::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $menu = "\n\nKetik angka/layanan:\n";
        foreach ($rules as $rule) {
            $menu .= "[{$rule->keyword}] {$rule->judul_menu}\n";
        }

        if ($punya_banyak_anak) {
            $menu .= "\nKetik 'GANTI ANAK' untuk mengganti pilihan anak.";
        }

        return rtrim($header . $menu);
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
            ->where('status', '!=', 'lunas')
            ->get();

        if ($tagihan->isEmpty()) {
            return "✅ *Info Tagihan Ananda {$siswa->nama_lengkap}*\n\nAlhamdulillah, tidak ada tunggakan. Terima kasih atas pembayarannya! 🙏";
        }

        $text  = "💳 *Info Tagihan Ananda {$siswa->nama_lengkap}*\n\nTagihan yang belum lunas:\n";
        $total = 0;
        foreach ($tagihan as $t) {
            $namaTagihan = $t->judul ?? $t->nama_tagihan ?? $t->jenis ?? 'Tagihan';
            $nominal = (float) $t->nominal;
            $text  .= "• {$namaTagihan}: *Rp " . number_format($nominal, 0, ',', '.') . "*\n";
            $total += $nominal;
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
            $tgl   = Carbon::parse($p->tanggal_publish ?? $p->created_at)->translatedFormat('d M Y');
            $isiRaw = (string) ($p->konten ?? $p->isi ?? '');
            $isi   = mb_substr($isiRaw, 0, 150) . (mb_strlen($isiRaw) > 150 ? '...' : '');
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
        // Strip @s.whatsapp.net atau @c.us jika ada (dari format Go-WA / legacy)
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
        string $intent,
        ?string $displayNoHp = null
    ): void {
        // Dispatch Job async
        SendWhatsappMessage::dispatch($noHp, $balasan);

        // Log percakapan
        $savedPhone = $displayNoHp ?: $this->toLokalFormat($this->normalisasiNomor($noHp));

        ChatbotLog::create([
            'no_hp'       => $savedPhone,
            'pesan_masuk' => $pesanMasuk,
            'pesan_keluar'=> $balasan,
            'siswa_id'    => $siswaId,
            'intent'      => $intent,
        ]);
    }
}
