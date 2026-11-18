<?php

namespace Database\Factories;

use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jadwal>
 */
class JadwalFactory extends Factory
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
            'shift_id' => Shift::factory(),
            'tanggal' => fake()->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
        ];
    }
}
