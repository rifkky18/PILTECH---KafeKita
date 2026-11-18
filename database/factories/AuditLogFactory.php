<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_aksi' => fake()->randomElement(['Membuat', 'Mengubah', 'Menghapus']),
            'nama_tabel' => fake()->randomElement(['karyawan', 'jadwal', 'absensi', 'cuti', 'lembur', 'penggajian']),
            'record_id' => fake()->numberBetween(1, 100000),
            'waktu_perubahan' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            'keterangan' => fake()->sentence(),
        ];
    }
}
