<?php

namespace Database\Factories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_shift' => fake()->randomElement(['Pagi', 'Siang', 'Sore', 'Malam']),
            'jam_masuk' => fake()->randomElement(['06:00:00', '08:00:00', '14:00:00', '16:00:00']),
            'jam_pulang' => fake()->randomElement(['14:00:00', '16:00:00', '22:00:00', '23:00:00']),
        ];
    }
}
