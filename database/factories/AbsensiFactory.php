<?php

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absensi>
 */
class AbsensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['hadir', 'terlambat', 'alpa']);

        return [
            'karyawan_id' => Karyawan::factory(),
            'tanggal' => fake()->dateTimeBetween('-1 year', 'today')->format('Y-m-d'),
            'jam_masuk' => $status === 'alpa' ? null : ($status === 'terlambat' ? '08:30:00' : '08:00:00'),
            'jam_pulang' => $status === 'alpa' ? null : '16:00:00',
            'status' => $status,
        ];
    }
}
