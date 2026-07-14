<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SiswaRequest;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    public function index(): View
    {
        $siswa = Siswa::with('kelas')
            ->when(request('q'), function ($query, string $q): void {
                $query->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            })
            ->when(request('kelas_id'), fn ($query, $id) => $query->where('kelas_id', $id))
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.index', compact('siswa', 'kelasList'));
    }

    public function create(): View
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.create', compact('kelas'));
    }

    public function store(SiswaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        Siswa::create($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load('kelas', 'orangTua', 'nilai.mapel', 'absensi.jadwal.mapel');

        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa): View
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(SiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('foto')) {
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $validated['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }
}
