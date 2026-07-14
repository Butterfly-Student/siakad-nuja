<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GuruRequest;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(): View
    {
        $guru = Guru::with('user')
            ->when(request('q'), function ($query, string $q): void {
                $query->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%");
            })
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        return view('guru.index', compact('guru'));
    }

    public function create(): View
    {
        return view('guru.create');
    }

    public function store(GuruRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'nama' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_GURU,
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
        $guru->load('user', 'kelasWali', 'jadwal.mapel', 'jadwal.kelas');

        return view('guru.show', compact('guru'));
    }

    public function edit(Guru $guru): View
    {
        $guru->load('user');

        return view('guru.edit', compact('guru'));
    }

    public function update(GuruRequest $request, Guru $guru): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($guru, $validated): void {
            $userData = [
                'nama' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? null,
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $guru->user->update($userData);

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
