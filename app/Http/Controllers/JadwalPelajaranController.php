<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(): View
    {
        $jadwal = JadwalPelajaran::with(['kelas', 'mapel', 'guru'])
            ->orderBy('hari')
            ->orderBy('jam_ke')
            ->paginate(15);

        return view('jadwal.index', compact('jadwal'));
    }

    public function create(): View
    {
        return view('jadwal.create', [
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'mapel' => MataPelajaran::orderBy('nama_mapel')->get(),
            'guru' => Guru::orderBy('nama_lengkap')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        JadwalPelajaran::create($validated);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show(JadwalPelajaran $jadwal): View
    {
        $jadwal->load('kelas', 'mapel', 'guru');

        return view('jadwal.show', compact('jadwal'));
    }

    public function edit(JadwalPelajaran $jadwal): View
    {
        return view('jadwal.edit', [
            'jadwal' => $jadwal,
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'mapel' => MataPelajaran::orderBy('nama_mapel')->get(),
            'guru' => Guru::orderBy('nama_lengkap')->get(),
        ]);
    }

    public function update(Request $request, JadwalPelajaran $jadwal): RedirectResponse
    {
        $validated = $this->validated($request);

        $jadwal->update($validated);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|string|max:15',
            'jam_ke' => 'required|integer|min:1|max:15',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
            'tahun_ajaran' => 'required|string|max:15',
        ]);
    }
}
