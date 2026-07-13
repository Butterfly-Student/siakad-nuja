<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index(): View
    {
        $nilai = Nilai::with(['siswa', 'mapel', 'kelas'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('nilai.index', compact('nilai'));
    }

    public function create(): View
    {
        return view('nilai.create', [
            'siswa' => Siswa::orderBy('nama_lengkap')->get(),
            'mapel' => MataPelajaran::orderBy('nama_mapel')->get(),
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['nilai_akhir'] = $this->hitungNilaiAkhir($validated);
        $validated['predikat'] = $this->hitungPredikat($validated['nilai_akhir']);

        Nilai::create($validated);

        return redirect()->route('nilai.index')->with('success', 'Nilai berhasil ditambahkan.');
    }

    public function show(Nilai $nilai): View
    {
        $nilai->load('siswa', 'mapel', 'kelas');

        return view('nilai.show', compact('nilai'));
    }

    public function edit(Nilai $nilai): View
    {
        return view('nilai.edit', [
            'nilai' => $nilai,
            'siswa' => Siswa::orderBy('nama_lengkap')->get(),
            'mapel' => MataPelajaran::orderBy('nama_mapel')->get(),
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
        ]);
    }

    public function update(Request $request, Nilai $nilai): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['nilai_akhir'] = $this->hitungNilaiAkhir($validated);
        $validated['predikat'] = $this->hitungPredikat($validated['nilai_akhir']);

        $nilai->update($validated);

        return redirect()->route('nilai.index')->with('success', 'Nilai berhasil diperbarui.');
    }

    public function destroy(Nilai $nilai): RedirectResponse
    {
        $nilai->delete();

        return redirect()->route('nilai.index')->with('success', 'Nilai berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|string|max:10',
            'tahun_ajaran' => 'required|string|max:15',
            'nilai_harian' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
        ]);
    }

    private function hitungNilaiAkhir(array $data): ?float
    {
        $harian = (float) ($data['nilai_harian'] ?? 0);
        $uts = (float) ($data['nilai_uts'] ?? 0);
        $uas = (float) ($data['nilai_uas'] ?? 0);

        if ($harian === 0.0 && $uts === 0.0 && $uas === 0.0) {
            return null;
        }

        return round(($harian * 0.3) + ($uts * 0.3) + ($uas * 0.4), 2);
    }

    private function hitungPredikat(?float $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        return match (true) {
            $nilai >= 90 => 'A',
            $nilai >= 80 => 'B',
            $nilai >= 70 => 'C',
            $nilai >= 60 => 'D',
            default => 'E',
        };
    }
}
