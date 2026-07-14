<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    private static array $bankList = [
        'BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'CIMB Niaga',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tagihan_id'    => Tagihan::factory(),
            'nominal'       => fake()->randomElement([150000, 175000, 200000, 250000]),
            'metode'        => 'Transfer',
            'bank'          => fake()->randomElement(self::$bankList),
            'nama_pengirim' => fake()->name(),
            'tanggal_bayar' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'bukti'         => null, // path file bukti, null di seeder
            'status'        => Pembayaran::STATUS_MENUNGGU,
            'catatan'       => null,
            'alasan_tolak'  => null,
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ];
    }

    /**
     * Pembayaran sudah disetujui.
     */
    public function disetujui(int $verifikatorId): static
    {
        return $this->state([
            'status'              => Pembayaran::STATUS_DISETUJUI,
            'catatan'             => 'Pembayaran telah dikonfirmasi.',
            'diverifikasi_oleh'   => $verifikatorId,
            'diverifikasi_pada'   => now()->subHours(fake()->numberBetween(1, 72)),
        ]);
    }

    /**
     * Pembayaran ditolak.
     */
    public function ditolak(int $verifikatorId): static
    {
        return $this->state([
            'status'            => Pembayaran::STATUS_DITOLAK,
            'alasan_tolak'      => fake()->randomElement([
                'Jumlah transfer tidak sesuai nominal tagihan.',
                'Bukti transfer tidak terbaca / buram.',
                'Nama pengirim tidak cocok dengan data siswa.',
            ]),
            'diverifikasi_oleh' => $verifikatorId,
            'diverifikasi_pada' => now()->subHours(fake()->numberBetween(1, 48)),
        ]);
    }
}
