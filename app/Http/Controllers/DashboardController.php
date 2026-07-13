<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pengumuman;
use App\Models\Siswa;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
            'total_kelas' => Kelas::count(),
            'total_mapel' => MataPelajaran::count(),
        ];

        $pengumumanTerbaru = Pengumuman::with('pembuat')
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'pengumumanTerbaru'));
    }
}
