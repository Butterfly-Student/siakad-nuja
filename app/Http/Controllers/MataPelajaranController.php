<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(): View
    {
        $mapel = MataPelajaran::orderBy('nama_mapel')->paginate(15);

        return view('mata_pelajaran.index', compact('mapel'));
    }

    public function create(): View
    {
        return view('mata_pelajaran.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            'jenjang' => 'required|string|max:20',
            'kkm' => 'nullable|integer|min:0|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(MataPelajaran $mataPelajaran): View
    {
        return view('mata_pelajaran.show', ['mapel' => $mataPelajaran]);
    }

    public function edit(MataPelajaran $mataPelajaran): View
    {
        return view('mata_pelajaran.edit', ['mapel' => $mataPelajaran]);
    }

    public function update(Request $request, MataPelajaran $mataPelajaran): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel,' . $mataPelajaran->id,
            'nama_mapel' => 'required|string|max:100',
            'jenjang' => 'required|string|max:20',
            'kkm' => 'nullable|integer|min:0|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $mataPelajaran->update($validated);

        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran): RedirectResponse
    {
        $mataPelajaran->delete();

        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
