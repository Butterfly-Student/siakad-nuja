<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(): View
    {
        $guru = Guru::with('user')->orderBy('nama_lengkap')->paginate(15);

        return view('guru.index', compact('guru'));
    }

    public function create(): View
    {
        return view('guru.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nip' => 'required|string|max:30|unique:guru,nip',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8',
            'jabatan' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'nama' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'guru',
                'no_hp' => $validated['no_hp'] ?? null,
                'is_active' => true,
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jabatan' => $validated['jabatan'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function show(Guru $guru): View
    {
        $guru->load('user', 'kelasWali', 'jadwal');

        return view('guru.show', compact('guru'));
    }

    public function edit(Guru $guru): View
    {
        $guru->load('user');

        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nip' => 'required|string|max:30|unique:guru,nip,' . $guru->id,
            'email' => 'required|email|max:150|unique:users,email,' . $guru->user_id,
            'jabatan' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($guru, $validated): void {
            $guru->user->update([
                'nama' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? null,
            ]);

            $guru->update([
                'nip' => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jabatan' => $validated['jabatan'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        DB::transaction(function () use ($guru): void {
            $user = $guru->user;
            $guru->delete();
            $user?->delete();
        });

        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus.');
    }
}
