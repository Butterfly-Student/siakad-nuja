<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Halaman form filter laporan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Jika guru, tampilkan hanya kelas dan mapel yang relevan
        // Untuk saat ini agar sederhana, kita ambil semua jika admin, atau sesuai hak jika guru
        if ($user->isGuru()) {
            $guru = $user->guru;
            // Kelas wali atau kelas di mana dia mengajar
            $kelasDiampu = $guru?->jadwal()->pluck('kelas_id')->toArray() ?? [];
            $kelasWali = $guru?->kelasWali()->pluck('id')->toArray() ?? [];
            $kelasIds = array_unique(array_merge($kelasDiampu, $kelasWali));

            $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('nama_kelas')->get();
            $mapelIds = $guru?->jadwal()->pluck('mapel_id')->toArray() ?? [];
            $mapel = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();
        } else {
            $kelas = Kelas::orderBy('nama_kelas')->get();
            $mapel = MataPelajaran::orderBy('nama_mapel')->get();
        }

        return view('laporan.index', compact('kelas', 'mapel'));
    }

    /**
     * Preview Rekap Kehadiran
     */
    public function kehadiran(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'bulan'    => 'required|date_format:Y-m', // e.g., 2025-07
            'export'   => 'nullable|in:pdf,csv',
        ]);

        $kelas = Kelas::findOrFail($validated['kelas_id']);
        $bulan = $validated['bulan'];

        // Ambil data siswa
        $siswa = Siswa::where('kelas_id', $kelas->id)->orderBy('nama_lengkap')->get();

        // Ambil rekap absen bulanan
        // Untuk sederhana, kita query group by
        $absensi = Absensi::with('jadwal')
            ->whereHas('jadwal', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })
            ->where('tanggal', 'like', $bulan . '-%')
            ->get();

        // Susun rekap: [siswa_id => ['Hadir' => x, 'Sakit' => y, 'Izin' => z, 'Alpa' => a]]
        $rekap = [];
        foreach ($siswa as $s) {
            $rekap[$s->id] = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
        }

        foreach ($absensi as $absen) {
            if (isset($rekap[$absen->siswa_id][$absen->status])) {
                $rekap[$absen->siswa_id][$absen->status]++;
            }
        }

        if ($request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf_kehadiran', compact('kelas', 'bulan', 'siswa', 'rekap'))->setPaper('a4', 'landscape');
            return $pdf->download('Rekap_Kehadiran_'.$kelas->nama_kelas.'_'.$bulan.'.pdf');
        }

        if ($request->input('export') === 'csv') {
            return $this->exportCsvKehadiran($kelas, $bulan, $siswa, $rekap);
        }

        return view('laporan.kehadiran', compact('kelas', 'bulan', 'siswa', 'rekap'));
    }

    /**
     * Preview Rekap Nilai
     */
    public function nilai(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'export'   => 'nullable|in:pdf,csv',
        ]);

        $kelas = Kelas::findOrFail($validated['kelas_id']);
        $mapel = MataPelajaran::findOrFail($validated['mapel_id']);

        $siswa = Siswa::where('kelas_id', $kelas->id)->orderBy('nama_lengkap')->get();
        $nilai = Nilai::where('kelas_id', $kelas->id)
            ->where('mapel_id', $mapel->id)
            ->get()
            ->keyBy('siswa_id');

        if ($request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf_nilai', compact('kelas', 'mapel', 'siswa', 'nilai'))->setPaper('a4', 'portrait');
            return $pdf->download('Rekap_Nilai_'.$kelas->nama_kelas.'_'.$mapel->kode_mapel.'.pdf');
        }

        if ($request->input('export') === 'csv') {
            return $this->exportCsvNilai($kelas, $mapel, $siswa, $nilai);
        }

        return view('laporan.nilai', compact('kelas', 'mapel', 'siswa', 'nilai'));
    }

    /**
     * Helper Ekspor CSV Kehadiran
     */
    private function exportCsvKehadiran($kelas, $bulan, $siswa, $rekap)
    {
        $filename = 'Rekap_Kehadiran_'.$kelas->nama_kelas.'_'.$bulan.'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($siswa, $rekap) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'NIS', 'Nama Siswa', 'Hadir', 'Sakit', 'Izin', 'Alpa']);
            foreach ($siswa as $i => $s) {
                fputcsv($file, [
                    $i + 1,
                    $s->nis,
                    $s->nama_lengkap,
                    $rekap[$s->id]['Hadir'],
                    $rekap[$s->id]['Sakit'],
                    $rekap[$s->id]['Izin'],
                    $rekap[$s->id]['Alpa'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper Ekspor CSV Nilai
     */
    private function exportCsvNilai($kelas, $mapel, $siswa, $nilai)
    {
        $filename = 'Rekap_Nilai_'.$kelas->nama_kelas.'_'.$mapel->kode_mapel.'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($siswa, $nilai) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'NIS', 'Nama Siswa', 'Harian', 'UTS', 'UAS', 'Akhir', 'Predikat']);
            foreach ($siswa as $i => $s) {
                $n = $nilai[$s->id] ?? null;
                fputcsv($file, [
                    $i + 1,
                    $s->nis,
                    $s->nama_lengkap,
                    $n ? $n->nilai_harian : '-',
                    $n ? $n->nilai_uts : '-',
                    $n ? $n->nilai_uas : '-',
                    $n ? $n->nilai_akhir : '-',
                    $n ? $n->predikat : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
