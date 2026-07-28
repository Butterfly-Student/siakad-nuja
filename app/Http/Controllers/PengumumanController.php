<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PengumumanRequest;
use App\Models\Kelas;
use App\Models\Pengumuman;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PengumumanController extends Controller
{
    public function index(): View
    {
        $pengumuman = Pengumuman::with(['pembuat', 'kelas'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('pengumuman.index', compact('pengumuman'));
    }

    public function create(): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('pengumuman.create', compact('kelasList'));
    }

    public function store(PengumumanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['dibuat_oleh'] = $request->user()->id;

        Pengumuman::create($validated);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function show(Pengumuman $pengumuman): View
    {
        $pengumuman->load(['pembuat', 'kelas']);

        return view('pengumuman.show', compact('pengumuman'));
    }

    public function edit(Pengumuman $pengumuman): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('pengumuman.edit', compact('pengumuman', 'kelasList'));
    }

    public function update(PengumumanRequest $request, Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->update($request->validated());

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
