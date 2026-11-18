<?php

namespace Database\Factories;

use App\Models\Jabatan;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Karyawan>
 */
class KaryawanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->numerify('###############'),
            'nama' => fake()->name(),
            'no_hp' => fake()->numerify('08##########'),
            'alamat' => fake()->address(),
            'tanggal_masuk' => fake()->dateTimeBetween('-5 years', 'today')->format('Y-m-d'),
            'jabatan_id' => Jabatan::factory(),
            'status' => fake()->randomElement(['aktif', 'nonaktif']),
        ];
    }
}
