<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\Penggajian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penggajian>
 */
class PenggajianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gajiPokok = fake()->numberBetween(3000000, 15000000);
        $lembur = fake()->numberBetween(0, 1500000);
        $potongan = fake()->numberBetween(0, 500000);
        $status = fake()->randomElement(['draft', 'final']);

        return [
            'karyawan_id' => Karyawan::factory(),
            'periode' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m'),
            'gaji_pokok' => $gajiPokok,
            'lembur' => $lembur,
            'potongan' => $potongan,
            'gaji_bersih' => $gajiPokok + $lembur - $potongan,
            'status' => $status,
            'finalized_at' => $status === 'final' ? now() : null,
        ];
    }
}
