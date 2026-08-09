<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\JadwalPelajaranRequest;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class JadwalPelajaranController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $jadwal = JadwalPelajaran::with(['kelas', 'mapel', 'guru'])
            ->when($user?->isGuru(), function ($query) use ($user): void {
                $guru = $user->guru;
                $query->where('guru_id', $guru?->id ?? 0);
            })
            ->when(request('kelas_id'), fn ($query, $id) => $query->where('kelas_id', $id))
            ->when(request('guru_id') && $user?->isAdmin(), fn ($query, $id) => $query->where('guru_id', $id))
            ->when(request('hari'), fn ($query, $hari) => $query->where('hari', $hari))
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
            ->orderBy('jam_ke')
            ->paginate(20)
            ->withQueryString();

        if ($user?->isGuru()) {
            $guruId = $user->guru?->id ?? 0;
            $kelasList = Kelas::whereIn('id', function ($q) use ($guruId): void {
                $q->select('kelas_id')->from('jadwal_pelajaran')->where('guru_id', $guruId);
            })->orWhere('wali_kelas_id', $guruId)->orderBy('nama_kelas')->get();
        } else {
            $kelasList = Kelas::orderBy('nama_kelas')->get();
        }

        $guruList = $user?->isAdmin() ? Guru::orderBy('nama_lengkap')->get() : collect();

        return view('jadwal.index', compact('jadwal', 'kelasList', 'guruList'));
    }

    public function create(): View
    {
        return view('jadwal.create', $this->formData());
    }

    public function store(JadwalPelajaranRequest $request): RedirectResponse
    {
        JadwalPelajaran::create($request->validated());

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show(JadwalPelajaran $jadwal): View
    {
        $user = request()->user();
        if ($user?->isGuru() && $jadwal->guru_id !== $user->guru?->id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $jadwal->load('kelas', 'mapel', 'guru');

        return view('jadwal.show', compact('jadwal'));
    }

    public function edit(JadwalPelajaran $jadwal): View
    {
        return view('jadwal.edit', ['jadwal' => $jadwal] + $this->formData());
    }

    public function update(JadwalPelajaranRequest $request, JadwalPelajaran $jadwal): RedirectResponse
    {
        $jadwal->update($request->validated());

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'mapel' => MataPelajaran::orderBy('nama_mapel')->get(),
            'guru' => Guru::orderBy('nama_lengkap')->get(),
        ];
    }
}
