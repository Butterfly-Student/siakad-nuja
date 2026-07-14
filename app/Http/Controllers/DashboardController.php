<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pengumuman;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $pengumumanTerbaru = Pengumuman::with('pembuat')
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        if ($user->isGuru()) {
            return $this->guruDashboard($request, $pengumumanTerbaru);
        }

        $stats = [
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
            'total_kelas' => Kelas::count(),
            'total_mapel' => MataPelajaran::count(),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'role' => 'admin',
        ]);
    }

    private function guruDashboard(Request $request, $pengumumanTerbaru): View
    {
        $guru = $request->user()->guru;

        $hariIni = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][(int) date('w')];

        $jadwalHariIni = $guru
            ? $guru->jadwal()->with(['mapel', 'kelas'])
                ->where('hari', $hariIni)
                ->orderBy('jam_ke')
                ->get()
            : collect();

        $kelasWali = $guru ? $guru->kelasWali()->withCount('siswa')->get() : collect();

        $stats = [
            'total_jadwal' => $guru ? $guru->jadwal()->count() : 0,
            'kelas_diampu' => $guru ? $guru->jadwal()->distinct('kelas_id')->count('kelas_id') : 0,
            'jadwal_hari_ini' => $jadwalHariIni->count(),
            'kelas_wali' => $kelasWali->count(),
        ];

        return view('dashboard', [
            'stats' => $stats,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'role' => 'guru',
            'jadwalHariIni' => $jadwalHariIni,
            'kelasWali' => $kelasWali,
            'hariIni' => $hariIni,
        ]);
    }
}
