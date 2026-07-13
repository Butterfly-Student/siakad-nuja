<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index(): View
    {
        $pengumuman = Pengumuman::with('pembuat')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('pengumuman.index', compact('pengumuman'));
    }

    public function create(): View
    {
        return view('pengumuman.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'konten' => 'required|string',
            'target_role' => 'nullable|string|max:20',
            'tanggal_publish' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['dibuat_oleh'] = $request->user()?->id ?? 1;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        Pengumuman::create($validated);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function show(Pengumuman $pengumuman): View
    {
        $pengumuman->load('pembuat');

        return view('pengumuman.show', compact('pengumuman'));
    }

    public function edit(Pengumuman $pengumuman): View
    {
        return view('pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'konten' => 'required|string',
            'target_role' => 'nullable|string|max:20',
            'tanggal_publish' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $pengumuman->update($validated);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
