<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder khusus untuk data tagihan & pembayaran.
 * Aman dijalankan berkali-kali (idempotent) — hanya menambah jika tabel kosong.
 */
class TagihanSeeder extends Seeder
{
    public function run(): void
    {
        // Jangan jalankan lagi kalau sudah ada data
        if (Tagihan::count() > 0) {
            $this->command->warn('Tabel tagihan sudah berisi data. TagihanSeeder dilewati.');
            return;
        }

        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            $this->command->error('Admin user tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $siswaList = Siswa::all();
        if ($siswaList->isEmpty()) {
            $this->command->error('Data siswa kosong. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $bulanList = [
            ['Mei 2025',  '2025-05-31', 3],
            ['Juni 2025', '2025-06-30', 2],
            ['Juli 2025', '2025-07-31', 1],
        ];
        $nominal = 200_000;

        $tagihanRows    = [];
        $pembayaranRows = [];

        foreach ($siswaList as $siswa) {
            foreach ($bulanList as [$periode, $jatuhTempo, $bulanLalu]) {
                $isOldest = $bulanLalu === 3;
                $isMid    = $bulanLalu === 2;

                if ($isOldest) {
                    $status = Tagihan::STATUS_LUNAS;
                } elseif ($isMid) {
                    $status = fake()->randomElement([Tagihan::STATUS_LUNAS, Tagihan::STATUS_MENUNGGU]);
                } else {
                    $status = fake()->randomElement([Tagihan::STATUS_BELUM, Tagihan::STATUS_MENUNGGU]);
                }

                $tagihan = Tagihan::create([
                    'siswa_id'    => $siswa->id,
                    'judul'       => 'SPP ' . $periode,
                    'jenis'       => 'SPP',
                    'periode'     => $periode,
                    'nominal'     => $nominal,
                    'jatuh_tempo' => $jatuhTempo,
                    'status'      => $status,
                    'keterangan'  => null,
                ]);

                if ($status === Tagihan::STATUS_LUNAS) {
                    Pembayaran::create([
                        'tagihan_id'        => $tagihan->id,
                        'nominal'           => $nominal,
                        'metode'            => 'Transfer',
                        'bank'              => fake()->randomElement(['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI']),
                        'nama_pengirim'     => $siswa->nama_lengkap,
                        'tanggal_bayar'     => now()->subMonths($bulanLalu)->addDays(fake()->numberBetween(1, 10))->format('Y-m-d'),
                        'bukti'             => null,
                        'status'            => Pembayaran::STATUS_DISETUJUI,
                        'catatan'           => 'Pembayaran telah dikonfirmasi.',
                        'diverifikasi_oleh' => $admin->id,
                        'diverifikasi_pada' => now()->subMonths($bulanLalu)->addDays(3),
                    ]);
                } elseif ($status === Tagihan::STATUS_MENUNGGU) {
                    Pembayaran::create([
                        'tagihan_id'    => $tagihan->id,
                        'nominal'       => $nominal,
                        'metode'        => 'Transfer',
                        'bank'          => fake()->randomElement(['BCA', 'BRI', 'BSI', 'Mandiri']),
                        'nama_pengirim' => $siswa->nama_lengkap,
                        'tanggal_bayar' => now()->subDays(fake()->numberBetween(1, 7))->format('Y-m-d'),
                        'bukti'         => null,
                        'status'        => Pembayaran::STATUS_MENUNGGU,
                        'catatan'       => null,
                    ]);
                }
            }
        }

        $count = Tagihan::count();
        $this->command->info("TagihanSeeder selesai: {$count} tagihan dibuat.");
    }
}
