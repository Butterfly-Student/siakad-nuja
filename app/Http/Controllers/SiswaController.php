<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(): View
    {
        $siswa = Siswa::with('kelas')->orderBy('nama_lengkap')->paginate(15);

        return view('siswa.index', compact('siswa'));
    }

    public function create(): View
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:siswa,nis',
            'nama_lengkap' => 'required|string|max:150',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P,Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'status' => 'nullable|string|max:20',
            'tahun_masuk' => 'required|integer|min:1990|max:2100',
        ]);

        Siswa::create($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa): View
    {
        $siswa->load('kelas', 'orangTua', 'nilai.mapel');

        return view('siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa): View
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:siswa,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:150',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P,Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'status' => 'nullable|string|max:20',
            'tahun_masuk' => 'required|integer|min:1990|max:2100',
        ]);

        $siswa->update($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa): RedirectResponse
    {
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }
}
