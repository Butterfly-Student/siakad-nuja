<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(): View
    {
        $absensi = Absensi::with(['siswa', 'jadwal.mapel'])
            ->orderByDesc('tanggal')
            ->paginate(15);

        return view('absensi.index', compact('absensi'));
    }

    public function create(): View
    {
        return view('absensi.create', [
            'siswa' => Siswa::orderBy('nama_lengkap')->get(),
            'jadwal' => JadwalPelajaran::with(['mapel', 'kelas'])->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'keterangan' => 'nullable|string',
        ]);

        Absensi::create($validated);

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil ditambahkan.');
    }

    public function show(Absensi $absensi): View
    {
        $absensi->load('siswa', 'jadwal.mapel');

        return view('absensi.show', compact('absensi'));
    }

    public function edit(Absensi $absensi): View
    {
        return view('absensi.edit', [
            'absensi' => $absensi,
            'siswa' => Siswa::orderBy('nama_lengkap')->get(),
            'jadwal' => JadwalPelajaran::with(['mapel', 'kelas'])->get(),
        ]);
    }

    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'keterangan' => 'nullable|string',
        ]);

        $absensi->update($validated);

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi): RedirectResponse
    {
        $absensi->delete();

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil dihapus.');
    }
}
