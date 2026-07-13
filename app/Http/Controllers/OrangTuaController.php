<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function index(): View
    {
        $orangTua = OrangTua::with('siswa')->orderBy('nama')->paginate(15);

        return view('orang_tua.index', compact('orangTua'));
    }

    public function create(): View
    {
        $siswa = Siswa::orderBy('nama_lengkap')->get();

        return view('orang_tua.create', compact('siswa'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama' => 'required|string|max:150',
            'hubungan' => 'nullable|string|max:30',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:100',
            'is_kontak_utama' => 'nullable|boolean',
        ]);

        $validated['is_kontak_utama'] = (bool) ($validated['is_kontak_utama'] ?? false);

        OrangTua::create($validated);

        return redirect()->route('orang-tua.index')->with('success', 'Orang tua berhasil ditambahkan.');
    }

    public function show(OrangTua $orangTua): View
    {
        $orangTua->load('siswa');

        return view('orang_tua.show', compact('orangTua'));
    }

    public function edit(OrangTua $orangTua): View
    {
        $siswa = Siswa::orderBy('nama_lengkap')->get();

        return view('orang_tua.edit', compact('orangTua', 'siswa'));
    }

    public function update(Request $request, OrangTua $orangTua): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama' => 'required|string|max:150',
            'hubungan' => 'nullable|string|max:30',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:100',
            'is_kontak_utama' => 'nullable|boolean',
        ]);

        $validated['is_kontak_utama'] = (bool) ($validated['is_kontak_utama'] ?? false);

        $orangTua->update($validated);

        return redirect()->route('orang-tua.index')->with('success', 'Orang tua berhasil diperbarui.');
    }

    public function destroy(OrangTua $orangTua): RedirectResponse
    {
        $orangTua->delete();

        return redirect()->route('orang-tua.index')->with('success', 'Orang tua berhasil dihapus.');
    }
}
