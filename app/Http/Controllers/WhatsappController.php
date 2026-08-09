<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatbotLog;
use App\Models\Konfigurasi;
use App\Models\NotifikasiWhatsapp;
use App\Services\WhatsappGatewayService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsappController extends Controller
{
    public function __construct(private readonly WhatsappGatewayService $gateway) {}

    /**
     * Halaman utama: Status koneksi + QR code (jika perlu scan)
     */
    public function index(): View
    {
        $status     = $this->gateway->getStatus();
        $qrUrl      = null;

        // Jika belum login, siapkan QR URL
        if (in_array($status['status'] ?? '', ['SCAN_QR', 'DISCONNECTED'])) {
            $qrUrl = $this->gateway->getQrCode();
        }

        $totalNotif  = NotifikasiWhatsapp::count();
        $totalGagal  = NotifikasiWhatsapp::where('status', 'gagal')->count();
        $totalSesi   = \App\Models\ChatbotSession::count();

        return view('whatsapp.index', compact('status', 'qrUrl', 'totalNotif', 'totalGagal', 'totalSesi'));
    }

    /**
     * AJAX endpoint — cek status koneksi Go-WA (polling dari frontend)
     */
    public function statusAjax(): \Illuminate\Http\JsonResponse
    {
        $status = $this->gateway->getStatus();
        $qrUrl  = null;

        if (in_array($status['status'] ?? '', ['SCAN_QR', 'DISCONNECTED'])) {
            $qrUrl = $this->gateway->getQrCode();
        }

        return response()->json([
            'status' => $status,
            'qr'     => $qrUrl,
        ]);
    }

    /**
     * Trigger QR login via Go-WA
     */
    public function login(): \Illuminate\Http\RedirectResponse
    {
        $qrUrl = $this->gateway->getQrCode();

        if ($qrUrl) {
            return redirect()->route('whatsapp.index')->with('success', 'QR Code berhasil dimuat. Silakan scan.');
        }

        return redirect()->route('whatsapp.index')->with('error', 'Gagal memuat QR Code. Cek koneksi Go-WA.');
    }

    /**
     * Logout device dari WhatsApp via Go-WA
     */
    public function logout(): \Illuminate\Http\RedirectResponse
    {
        $success = $this->gateway->logout();

        return redirect()->route('whatsapp.index')->with(
            $success ? 'success' : 'error',
            $success ? 'Berhasil logout dari WhatsApp.' : 'Gagal logout. Cek koneksi Go-WA.'
        );
    }

    /**
     * Reconnect device ke WhatsApp via Go-WA
     */
    public function reconnect(): \Illuminate\Http\RedirectResponse
    {
        $success = $this->gateway->reconnect();

        return redirect()->route('whatsapp.index')->with(
            $success ? 'success' : 'error',
            $success ? 'Berhasil reconnect ke WhatsApp.' : 'Gagal reconnect. Cek koneksi Go-WA.'
        );
    }

    /**
     * Manajemen template pesan WA
     */
    public function templates(): View
    {
        $templateDefinitions = [
            'template_absensi' => [
                'label'   => 'Notifikasi Absensi & Kehadiran',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {kelas}, {status}, {hari}, {tanggal}, {keterangan}',
                'default' => "🔔 *Notifikasi Kehadiran*\nYth. Bpk/Ibu {nama_wali},\n\nAnanda *{nama_siswa}* ({kelas}) tercatat *{status}* pada hari {hari}, {tanggal}.\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_nilai' => [
                'label'   => 'Notifikasi Nilai Rapor Baru',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {kelas}, {mapel}, {nilai_harian}, {nilai_uts}, {nilai_uas}, {nilai_akhir}, {predikat}',
                'default' => "📊 *Notifikasi Nilai Baru*\nYth. Bpk/Ibu {nama_wali},\n\nNilai *{mapel}* Ananda *{nama_siswa}* telah diinput:\n• Tugas  : {nilai_harian}\n• UTS    : {nilai_uts}\n• UAS    : {nilai_uas}\n• *Nilai Akhir : {nilai_akhir} ({predikat})*\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_tagihan' => [
                'label'   => 'Notifikasi Tagihan Pembayaran',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {nama_tagihan}, {nominal}',
                'default' => "💳 *Notifikasi Tagihan*\nYth. Bpk/Ibu {nama_wali},\n\nTagihan baru untuk Ananda *{nama_siswa}*:\n• Nama   : {nama_tagihan}\n• Nominal: Rp {nominal}\n\nMohon untuk segera melakukan pembayaran.\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_pengumuman' => [
                'label'   => 'Notifikasi Broadcast Pengumuman',
                'hint'    => 'Variabel: {judul}, {isi}, {tanggal}',
                'default' => "📢 *Pengumuman Sekolah*\n*{judul}*\n\n{isi}\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_kuitansi' => [
                'label'   => 'Notifikasi Pembayaran Lunas (Kuitansi)',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {nama_tagihan}, {nominal}, {tanggal_bayar}',
                'default' => "✅ *Pembayaran Berhasil*\nYth. Bpk/Ibu {nama_wali},\n\nPembayaran *{nama_tagihan}* Ananda *{nama_siswa}* sebesar *Rp {nominal}* pada {tanggal_bayar} telah dikonfirmasi LUNAS.\n\nTerima kasih! 🙏\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_teguran' => [
                'label'   => 'Notifikasi Teguran & Peringatan Wali',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {keterangan}, {tanggal}',
                'default' => "⚠️ *Pemberitahuan Teguran Sekolah*\nYth. Bpk/Ibu {nama_wali},\n\nDisampaikan mengenai Ananda *{nama_siswa}*:\n{keterangan}\n\nTanggal: {tanggal}\n\nMohon kerjasamanya untuk perhatian Bpk/Ibu.\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'cs_whatsapp' => [
                'label'   => 'Nomor Customer Service / Admin WA',
                'hint'    => 'Nomor WhatsApp admin yang akan ditampilkan pada menu chatbot CS (contoh: 081234567890)',
                'default' => '081234567890',
            ],
        ];

        $templates = [];
        foreach ($templateDefinitions as $key => $def) {
            $templates[$key] = [
                'key'   => $key,
                'label' => $def['label'],
                'hint'  => $def['hint'],
                'value' => Konfigurasi::get($key, $def['default']),
            ];
        }

        return view('whatsapp.templates', compact('templates'));
    }

    /**
     * Form edit template tunggal
     */
    public function editTemplate(string $key): View
    {
        $all = $this->getTemplateDefinitions();
        if (! isset($all[$key])) {
            abort(404, 'Template tidak ditemukan.');
        }

        $def = $all[$key];
        $template = [
            'key'   => $key,
            'label' => $def['label'],
            'hint'  => $def['hint'],
            'value' => Konfigurasi::get($key, $def['default']),
        ];

        return view('whatsapp.edit_template', compact('template'));
    }

    /**
     * Update template tunggal
     */
    public function updateSingleTemplate(string $key, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        Konfigurasi::updateOrCreate(['key' => $key], ['value' => $request->input('value')]);

        return redirect()->route('whatsapp.templates')->with('success', 'Template pesan berhasil diperbarui.');
    }

    /**
     * Simpan semua template
     */
    public function updateTemplates(Request $request): \Illuminate\Http\RedirectResponse
    {
        $keys = ['template_absensi', 'template_nilai', 'template_tagihan', 'template_pengumuman', 'template_kuitansi', 'template_teguran', 'cs_whatsapp'];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Konfigurasi::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
            }
        }

        return redirect()->route('whatsapp.templates')->with('success', 'Template pesan berhasil disimpan.');
    }

    private function getTemplateDefinitions(): array
    {
        return [
            'template_absensi' => [
                'label'   => 'Notifikasi Absensi & Kehadiran',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {kelas}, {status}, {hari}, {tanggal}, {keterangan}',
                'default' => "🔔 *Notifikasi Kehadiran*\nYth. Bpk/Ibu {nama_wali},\n\nAnanda *{nama_siswa}* ({kelas}) tercatat *{status}* pada hari {hari}, {tanggal}.\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_nilai' => [
                'label'   => 'Notifikasi Nilai Rapor Baru',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {kelas}, {mapel}, {nilai_harian}, {nilai_uts}, {nilai_uas}, {nilai_akhir}, {predikat}',
                'default' => "📊 *Notifikasi Nilai Baru*\nYth. Bpk/Ibu {nama_wali},\n\nNilai *{mapel}* Ananda *{nama_siswa}* telah diinput:\n• Tugas  : {nilai_harian}\n• UTS    : {nilai_uts}\n• UAS    : {nilai_uas}\n• *Nilai Akhir : {nilai_akhir} ({predikat})*\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_tagihan' => [
                'label'   => 'Notifikasi Tagihan Pembayaran',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {nama_tagihan}, {nominal}',
                'default' => "💳 *Notifikasi Tagihan*\nYth. Bpk/Ibu {nama_wali},\n\nTagihan baru untuk Ananda *{nama_siswa}*:\n• Nama   : {nama_tagihan}\n• Nominal: Rp {nominal}\n\nMohon untuk segera melakukan pembayaran.\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_pengumuman' => [
                'label'   => 'Notifikasi Broadcast Pengumuman',
                'hint'    => 'Variabel: {judul}, {isi}, {tanggal}',
                'default' => "📢 *Pengumuman Sekolah*\n*{judul}*\n\n{isi}\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_kuitansi' => [
                'label'   => 'Notifikasi Pembayaran Lunas (Kuitansi)',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {nama_tagihan}, {nominal}, {tanggal_bayar}',
                'default' => "✅ *Pembayaran Berhasil*\nYth. Bpk/Ibu {nama_wali},\n\nPembayaran *{nama_tagihan}* Ananda *{nama_siswa}* sebesar *Rp {nominal}* pada {tanggal_bayar} telah dikonfirmasi LUNAS.\n\nTerima kasih! 🙏\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'template_teguran' => [
                'label'   => 'Notifikasi Teguran & Peringatan Wali',
                'hint'    => 'Variabel: {nama_wali}, {nama_siswa}, {keterangan}, {tanggal}',
                'default' => "⚠️ *Pemberitahuan Teguran Sekolah*\nYth. Bpk/Ibu {nama_wali},\n\nDisampaikan mengenai Ananda *{nama_siswa}*:\n{keterangan}\n\nTanggal: {tanggal}\n\nMohon kerjasamanya untuk perhatian Bpk/Ibu.\n\n— SIAKAD Nurul Jadid Karduluk",
            ],
            'cs_whatsapp' => [
                'label'   => 'Nomor Customer Service / Admin WA',
                'hint'    => 'Nomor WhatsApp admin yang akan ditampilkan pada menu chatbot CS (contoh: 081234567890)',
                'default' => '081234567890',
            ],
        ];
    }

    /**
     * Log notifikasi otomatis (outbound)
     */
    public function logNotifikasi(Request $request): View
    {
        $query = NotifikasiWhatsapp::with(['orangTua', 'siswa'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $notifikasi = $query->paginate(20)->withQueryString();

        return view('whatsapp.log-notifikasi', compact('notifikasi'));
    }

    /**
     * Kirim ulang notifikasi yang gagal
     */
    public function resend(NotifikasiWhatsapp $notifikasi): \Illuminate\Http\RedirectResponse
    {
        $success = $this->gateway->resendNotification($notifikasi);

        return redirect()->back()->with(
            $success ? 'success' : 'error',
            $success ? 'Pesan berhasil dikirim ulang.' : 'Gagal mengirim ulang. Cek koneksi Go-WA.'
        );
    }

    /**
     * Log percakapan chatbot (inbound + outbound)
     */
    public function logChatbot(Request $request): View
    {
        $query = ChatbotLog::with('siswa')->orderBy('id', 'desc');

        if ($request->filled('no_hp')) {
            $query->where('no_hp', 'like', '%' . $request->no_hp . '%');
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('whatsapp.log-chatbot', compact('logs'));
    }

    /**
     * Manajemen Rule Chatbot (Daftar Rule & Menu)
     */
    public function chatbotRules(): View
    {
        $rules = \App\Models\ChatbotRule::when(request('q'), function ($query, string $q): void {
            $query->where('keyword', 'like', "%{$q}%")
                ->orWhere('judul_menu', 'like', "%{$q}%");
        })
        ->orderBy('urutan')
        ->orderBy('id')
        ->paginate(15)
        ->withQueryString();

        return view('whatsapp.rules.index', compact('rules'));
    }

    public function createChatbotRule(): View
    {
        return view('whatsapp.rules.create');
    }

    public function storeChatbotRule(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'keyword'     => ['required', 'string', 'max:50', 'unique:chatbot_rules,keyword'],
            'judul_menu'  => ['required', 'string', 'max:150'],
            'tipe_action' => ['required', 'in:system_query,static_text'],
            'action_key'  => ['nullable', 'required_if:tipe_action,system_query', 'string', 'max:50'],
            'isi_balasan' => ['nullable', 'required_if:tipe_action,static_text', 'string'],
            'urutan'      => ['required', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        \App\Models\ChatbotRule::create($validated);

        return redirect()->route('whatsapp.chatbot-rules')->with('success', 'Rule chatbot berhasil ditambahkan.');
    }

    public function editChatbotRule(\App\Models\ChatbotRule $rule): View
    {
        return view('whatsapp.rules.edit', compact('rule'));
    }

    public function updateChatbotRule(Request $request, \App\Models\ChatbotRule $rule): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'keyword'     => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('chatbot_rules', 'keyword')->ignore($rule->id)],
            'judul_menu'  => ['required', 'string', 'max:150'],
            'tipe_action' => ['required', 'in:system_query,static_text'],
            'action_key'  => ['nullable', 'required_if:tipe_action,system_query', 'string', 'max:50'],
            'isi_balasan' => ['nullable', 'required_if:tipe_action,static_text', 'string'],
            'urutan'      => ['required', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $rule->update($validated);

        return redirect()->route('whatsapp.chatbot-rules')->with('success', 'Rule chatbot berhasil diperbarui.');
    }

    public function destroyChatbotRule(\App\Models\ChatbotRule $rule): \Illuminate\Http\RedirectResponse
    {
        $rule->delete();

        return redirect()->route('whatsapp.chatbot-rules')->with('success', 'Rule chatbot berhasil dihapus.');
    }
}
