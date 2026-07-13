<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::with('waliKelas')->orderBy('tingkat')->orderBy('nama_kelas')->paginate(15);

        return view('kelas.index', compact('kelas'));
    }

    public function create(): View
    {
        $guru = Guru::orderBy('nama_lengkap')->get();

        return view('kelas.create', compact('guru'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat' => 'required|string|max:10',
            'jenjang' => 'required|string|max:20',
            'tahun_ajaran' => 'required|string|max:15',
            'wali_kelas_id' => 'nullable|exists:guru,id',
            'kapasitas' => 'nullable|integer|min:1|max:255',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kela): View
    {
        $kela->load('waliKelas', 'siswa');

        return view('kelas.show', ['kelas' => $kela]);
    }

    public function edit(Kelas $kela): View
    {
        $guru = Guru::orderBy('nama_lengkap')->get();

        return view('kelas.edit', ['kelas' => $kela, 'guru' => $guru]);
    }

    public function update(Request $request, Kelas $kela): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50',
            'tingkat' => 'required|string|max:10',
            'jenjang' => 'required|string|max:20',
            'tahun_ajaran' => 'required|string|max:15',
            'wali_kelas_id' => 'nullable|exists:guru,id',
            'kapasitas' => 'nullable|integer|min:1|max:255',
        ]);

        $kela->update($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela): RedirectResponse
    {
        $kela->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
