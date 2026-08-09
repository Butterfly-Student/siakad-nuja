<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrangTuaRequest;
use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrangTuaController extends Controller
{
    public function index(): View
    {
        $orangTua = OrangTua::with('siswa')
            ->when(request('q'), function ($query, string $q): void {
                $query->where('nama', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('orang_tua.index', compact('orangTua'));
    }

    public function create(): View
    {
        $siswa = Siswa::orderBy('nama_lengkap')->get();

        return view('orang_tua.create', compact('siswa'));
    }

    public function store(OrangTuaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (! empty($data['is_kontak_utama']) && ! empty($data['siswa_id'])) {
            OrangTua::where('siswa_id', $data['siswa_id'])->update(['is_kontak_utama' => false]);
        }

        OrangTua::create($data);

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

    public function update(OrangTuaRequest $request, OrangTua $orangTua): RedirectResponse
    {
        $data = $request->validated();
        $siswaId = $data['siswa_id'] ?? $orangTua->siswa_id;

        if (! empty($data['is_kontak_utama']) && $siswaId) {
            OrangTua::where('siswa_id', $siswaId)
                ->where('id', '!=', $orangTua->id)
                ->update(['is_kontak_utama' => false]);
        }

        $orangTua->update($data);

        return redirect()->route('orang-tua.index')->with('success', 'Orang tua berhasil diperbarui.');
    }

    public function destroy(OrangTua $orangTua): RedirectResponse
    {
        $orangTua->delete();

        return redirect()->route('orang-tua.index')->with('success', 'Orang tua berhasil dihapus.');
    }
}
