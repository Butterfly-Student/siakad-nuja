<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $absensi = Absensi::with(['siswa', 'jadwal.mapel', 'jadwal.kelas'])
            ->when($user->isGuru(), function ($query) use ($user): void {
                $jadwalIds = $this->jadwalIdsUntukGuru($user);
                $query->whereIn('jadwal_id', $jadwalIds ?: [0]);
            })
            ->when($request->input('jadwal_id'), fn ($q, $id) => $q->where('jadwal_id', $id))
            ->when($request->input('tanggal'), fn ($q, $tgl) => $q->whereDate('tanggal', $tgl))
            ->orderByDesc('tanggal')
            ->orderBy('siswa_id')
            ->paginate(20)
            ->withQueryString();

        return view('absensi.index', compact('absensi'));
    }

    /**
     * Langkah 1: pilih jadwal + tanggal.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Absensi::class);

        $jadwal = $this->jadwalTerpilih($request)
            ->with(['mapel', 'kelas'])
            ->orderBy('kelas_id')
            ->get();

        return view('absensi.create', compact('jadwal'));
    }

    /**
     * Langkah 2: tampilkan daftar siswa pada kelas jadwal tsb untuk entri massal.
     */
    public function roster(Request $request): View
    {
        $this->authorize('create', Absensi::class);

        $validated = $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal_pelajaran,id'],
            'tanggal' => ['required', 'date'],
        ]);

        $jadwal = JadwalPelajaran::with(['mapel', 'kelas'])->findOrFail($validated['jadwal_id']);

        // Pastikan guru berwenang atas jadwal ini.
        $this->authorizeJadwal($request, (int) $jadwal->id);

        $siswa = Siswa::where('kelas_id', $jadwal->kelas_id)
            ->orderBy('nama_lengkap')
            ->get();

        $existing = Absensi::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->get()
            ->keyBy('siswa_id');

        return view('absensi.roster', [
            'jadwal' => $jadwal,
            'tanggal' => $validated['tanggal'],
            'siswa' => $siswa,
            'existing' => $existing,
        ]);
    }

    /**
     * Simpan absensi massal (upsert per siswa + jadwal + tanggal).
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Absensi::class);

        $validated = $request->validate([
            'jadwal_id' => ['required', 'exists:jadwal_pelajaran,id'],
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => [Rule::in(['Hadir', 'Izin', 'Sakit', 'Alpa'])],
            'keterangan' => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:255'],
        ]);

        $this->authorizeJadwal($request, (int) $validated['jadwal_id']);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['status'] as $siswaId => $status) {
                Absensi::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'jadwal_id' => $validated['jadwal_id'],
                        'tanggal' => $validated['tanggal'],
                    ],
                    [
                        'status' => $status,
                        'keterangan' => $validated['keterangan'][$siswaId] ?? null,
                    ],
                );
            }
        });

        return redirect()
            ->route('absensi.index', ['jadwal_id' => $validated['jadwal_id'], 'tanggal' => $validated['tanggal']])
            ->with('success', 'Absensi berhasil disimpan.');
    }

    public function show(Absensi $absensi): View
    {
        $this->authorize('view', $absensi);

        $absensi->load('siswa', 'jadwal.mapel', 'jadwal.kelas');

        return view('absensi.show', compact('absensi'));
    }

    public function destroy(Absensi $absensi): RedirectResponse
    {
        $this->authorize('delete', $absensi);

        $absensi->delete();

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Pastikan user berwenang atas jadwal tertentu (admin selalu boleh).
     */
    private function authorizeJadwal(Request $request, int $jadwalId): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        $policy = new \App\Policies\AbsensiPolicy();

        if (! $policy->mengampuJadwal($user, $jadwalId)) {
            abort(403, 'Anda tidak berwenang atas jadwal ini.');
        }
    }

    /**
     * Query jadwal yang boleh diakses guru (yang diampu atau kelas yang diwalikan).
     */
    private function jadwalTerpilih(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $user = $request->user();
        $query = JadwalPelajaran::query();

        if ($user->isGuru()) {
            $ids = $this->jadwalIdsUntukGuru($user);
            $query->whereIn('id', $ids ?: [0]);
        }

        return $query;
    }

    /**
     * @return array<int, int>
     */
    private function jadwalIdsUntukGuru(\App\Models\User $user): array
    {
        $guru = $user->guru;

        if ($guru === null) {
            return [];
        }

        $diampu = $guru->jadwal()->pluck('id')->all();
        $kelasWaliIds = $guru->kelasWali()->pluck('id')->all();
        $waliJadwal = JadwalPelajaran::whereIn('kelas_id', $kelasWaliIds)->pluck('id')->all();

        return array_values(array_unique([...$diampu, ...$waliJadwal]));
    }
}
