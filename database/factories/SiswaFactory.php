<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jk = fake()->randomElement(['L', 'P']);

        return [
            'nis' => fake()->unique()->numerify('2024####'),
            'nama_lengkap' => $jk === 'L' ? fake()->name('male') : fake()->name('female'),
            'kelas_id' => null,
            'tanggal_lahir' => fake()->dateTimeBetween('-15 years', '-12 years')->format('Y-m-d'),
            'jenis_kelamin' => $jk,
            'alamat' => fake()->address(),
            'foto' => null,
            'status' => 'Aktif',
            'tahun_masuk' => 2024,
        ];
    }
}
