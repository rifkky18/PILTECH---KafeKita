<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\Lembur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lembur>
 */
class LemburFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'karyawan_id' => Karyawan::factory(),
            'tanggal' => fake()->dateTimeBetween('-1 year', 'today')->format('Y-m-d'),
            'jumlah_jam' => fake()->randomFloat(2, 1, 4),
            'keterangan' => fake()->sentence(),
            'status' => fake()->randomElement(['menunggu', 'disetujui', 'ditolak']),
            'approver_id' => null,
            'tanggal_keputusan' => null,
        ];
    }
}
