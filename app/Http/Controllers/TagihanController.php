<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TagihanRequest;
use App\Http\Requests\VerifikasiPembayaranRequest;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    /**
     * Daftar tagihan dengan ringkasan 4 kartu.
     */
    public function index(Request $request): View
    {
        $query = Tagihan::with(['siswa.kelas', 'pembayaran'])
            ->latest();

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn ($q) => $q->where('kelas_id', $request->kelas_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->whereHas('siswa', fn ($q) => $q->where('nama_lengkap', 'like', "%{$request->q}%"));
        }

        $tagihan = $query->paginate(20)->withQueryString();

        $summary = [
            'total'      => Tagihan::count(),
            'lunas'      => Tagihan::where('status', Tagihan::STATUS_LUNAS)->count(),
            'menunggu'   => Tagihan::where('status', Tagihan::STATUS_MENUNGGU)->count(),
            'tunggakan'  => Tagihan::where('status', '!=', Tagihan::STATUS_LUNAS)
                ->whereNotNull('jatuh_tempo')
                ->where('jatuh_tempo', '<', now())
                ->count(),
        ];

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('tagihan.index', compact('tagihan', 'summary', 'kelasList'));
    }

    /**
     * Form buat tagihan baru.
     */
    public function create(): View
    {
        $siswaList = Siswa::with('kelas')->orderBy('nama_lengkap')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $tagihan   = new Tagihan();

        return view('tagihan.create', compact('siswaList', 'kelasList', 'tagihan'));
    }

    /**
     * Simpan tagihan — bisa single atau bulk (per kelas).
     */
    public function store(TagihanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->filled('kelas_id_massal') && $request->kelas_id_massal !== '') {
            // Tagihan massal: buat untuk semua siswa dalam kelas
            $siswas = Siswa::where('kelas_id', $request->kelas_id_massal)->get();

            foreach ($siswas as $s) {
                Tagihan::create([
                    'siswa_id'    => $s->id,
                    'judul'       => $data['judul'],
                    'jenis'       => $data['jenis'],
                    'periode'     => $data['periode'],
                    'nominal'     => $data['nominal'],
                    'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
                    'status'      => Tagihan::STATUS_BELUM,
                    'keterangan'  => $data['keterangan'] ?? null,
                ]);
            }

            return redirect()->route('tagihan.index')
                ->with('success', "Tagihan massal dibuat untuk {$siswas->count()} siswa.");
        }

        // Tagihan per siswa
        Tagihan::create([
            'siswa_id'    => $data['siswa_id'],
            'judul'       => $data['judul'],
            'jenis'       => $data['jenis'],
            'periode'     => $data['periode'],
            'nominal'     => $data['nominal'],
            'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
            'status'      => Tagihan::STATUS_BELUM,
            'keterangan'  => $data['keterangan'] ?? null,
        ]);

        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil dibuat.');
    }

    /**
     * Detail tagihan + riwayat pembayaran.
     */
    public function show(Tagihan $tagihan): View
    {
        $tagihan->load(['siswa.kelas', 'pembayaran.verifikator']);

        return view('tagihan.show', compact('tagihan'));
    }

    /**
     * Form edit tagihan.
     */
    public function edit(Tagihan $tagihan): View
    {
        $siswaList = Siswa::with('kelas')->orderBy('nama_lengkap')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('tagihan.edit', compact('tagihan', 'siswaList', 'kelasList'));
    }

    /**
     * Update tagihan.
     */
    public function update(TagihanRequest $request, Tagihan $tagihan): RedirectResponse
    {
        $tagihan->update($request->validated());

        return redirect()->route('tagihan.show', $tagihan)
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Hapus tagihan.
     */
    public function destroy(Tagihan $tagihan): RedirectResponse
    {
        $tagihan->delete();

        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan dihapus.');
    }

    /**
     * Verifikasi pembayaran: setujui.
     */
    public function verifikasi(VerifikasiPembayaranRequest $request, Pembayaran $pembayaran): RedirectResponse
    {
        $pembayaran->update([
            'status'              => Pembayaran::STATUS_DISETUJUI,
            'catatan'             => $request->catatan,
            'diverifikasi_oleh'   => auth()->id(),
            'diverifikasi_pada'   => now(),
        ]);

        $pembayaran->tagihan->update(['status' => Tagihan::STATUS_LUNAS]);

        return redirect()->route('tagihan.show', $pembayaran->tagihan_id)
            ->with('success', 'Pembayaran berhasil diverifikasi. Tagihan ditandai lunas.');
    }

    /**
     * Tolak pembayaran.
     */
    public function tolak(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $request->validate(['alasan_tolak' => 'required|string|max:500']);

        $pembayaran->update([
            'status'            => Pembayaran::STATUS_DITOLAK,
            'alasan_tolak'      => $request->alasan_tolak,
            'diverifikasi_oleh' => auth()->id(),
            'diverifikasi_pada' => now(),
        ]);

        // Kembalikan status tagihan ke belum dibayar
        $pembayaran->tagihan->update(['status' => Tagihan::STATUS_BELUM]);

        return redirect()->route('tagihan.show', $pembayaran->tagihan_id)
            ->with('error', 'Pembayaran ditolak.');
    }
}
