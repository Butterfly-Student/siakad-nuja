<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrangTua;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrangTua>
 */
class OrangTuaFactory extends Factory
{
    protected $model = OrangTua::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'siswa_id' => null,
            'nama' => fake()->name(),
            'hubungan' => fake()->randomElement(['Ayah', 'Ibu', 'Wali']),
            'no_hp' => '08' . fake()->numerify('##########'),
            'alamat' => fake()->address(),
            'pekerjaan' => fake()->randomElement(['Petani', 'Wiraswasta', 'PNS', 'Guru', 'Pedagang', 'Karyawan Swasta', 'Nelayan', 'Buruh']),
            'is_kontak_utama' => false,
        ];
    }
}
