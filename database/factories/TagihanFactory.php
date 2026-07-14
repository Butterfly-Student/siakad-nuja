<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tagihan>
 */
class TagihanFactory extends Factory
{
    protected $model = Tagihan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $bulan     = fake()->randomElement($bulanList);
        $tahun     = 2024;

        return [
            'siswa_id'    => Siswa::factory(),
            'judul'       => 'SPP ' . $bulan . ' ' . $tahun,
            'jenis'       => fake()->randomElement(['SPP', 'Uang Gedung', 'Kegiatan']),
            'periode'     => $bulan . ' ' . $tahun,
            'nominal'     => fake()->randomElement([150000, 175000, 200000, 250000]),
            'jatuh_tempo' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'status'      => fake()->randomElement([
                Tagihan::STATUS_BELUM,
                Tagihan::STATUS_BELUM,
                Tagihan::STATUS_MENUNGGU,
                Tagihan::STATUS_LUNAS,
            ]),
            'keterangan'  => null,
        ];
    }

    /**
     * Tagihan sudah lunas.
     */
    public function lunas(): static
    {
        return $this->state(['status' => Tagihan::STATUS_LUNAS]);
    }

    /**
     * Tagihan menunggu verifikasi.
     */
    public function menungguVerifikasi(): static
    {
        return $this->state(['status' => Tagihan::STATUS_MENUNGGU]);
    }

    /**
     * Tagihan belum dibayar.
     */
    public function belumDibayar(): static
    {
        return $this->state(['status' => Tagihan::STATUS_BELUM]);
    }

    /**
     * Tagihan dengan jatuh tempo sudah lewat (tunggakan).
     */
    public function tunggakan(): static
    {
        return $this->state([
            'status'      => Tagihan::STATUS_BELUM,
            'jatuh_tempo' => fake()->dateTimeBetween('-60 days', '-1 day')->format('Y-m-d'),
        ]);
    }
}
