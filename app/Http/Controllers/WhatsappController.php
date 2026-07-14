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
        $qrCode     = null;

        if (in_array($status['status'] ?? '', ['SCAN_QR', 'STARTING'])) {
            $qrCode = $this->gateway->getQrCode();
        }

        $totalNotif  = NotifikasiWhatsapp::count();
        $totalGagal  = NotifikasiWhatsapp::where('status', 'gagal')->count();
        $totalSesi   = \App\Models\ChatbotSession::count();

        return view('whatsapp.index', compact('status', 'qrCode', 'totalNotif', 'totalGagal', 'totalSesi'));
    }

    /**
     * AJAX endpoint — cek status koneksi WAHA (polling dari frontend)
     */
    public function statusAjax(): \Illuminate\Http\JsonResponse
    {
        $status = $this->gateway->getStatus();
        $qrCode = null;

        if (in_array($status['status'] ?? '', ['SCAN_QR', 'STARTING'])) {
            $qrCode = $this->gateway->getQrCode();
        }

        return response()->json([
            'status' => $status,
            'qr'     => $qrCode ? "data:image/png;base64,{$qrCode}" : null,
        ]);
    }

    /**
     * Manajemen template pesan WA
     */
    public function templates(): View
    {
        $templateKeys = [
            'template_absensi'   => 'Notifikasi Absensi',
            'template_nilai'     => 'Notifikasi Nilai Baru',
            'template_tagihan'   => 'Notifikasi Tagihan Baru',
            'template_pengumuman'=> 'Notifikasi Pengumuman',
            'template_kuitansi'  => 'Notifikasi Pembayaran Lunas',
            'cs_whatsapp'        => 'Nomor Customer Service',
        ];

        $templates = [];
        foreach ($templateKeys as $key => $label) {
            $templates[$key] = [
                'label' => $label,
                'value' => Konfigurasi::get($key, ''),
            ];
        }

        return view('whatsapp.templates', compact('templates'));
    }

    /**
     * Simpan semua template
     */
    public function updateTemplates(Request $request): \Illuminate\Http\RedirectResponse
    {
        $keys = ['template_absensi', 'template_nilai', 'template_tagihan', 'template_pengumuman', 'template_kuitansi', 'cs_whatsapp'];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Konfigurasi::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
            }
        }

        return redirect()->route('whatsapp.templates')->with('success', 'Template pesan berhasil disimpan.');
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
            $success ? 'Pesan berhasil dikirim ulang.' : 'Gagal mengirim ulang. Cek koneksi WAHA.'
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
}
