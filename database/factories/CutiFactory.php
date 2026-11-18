<?php

namespace Database\Factories;

use App\Models\Cuti;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cuti>
 */
class CutiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalMulai = fake()->dateTimeBetween('-1 year', '+30 days');
        $tanggalPengajuan = (clone $tanggalMulai)->modify('-5 days');
        $status = fake()->randomElement(['menunggu', 'disetujui', 'ditolak']);

        return [
            'karyawan_id' => Karyawan::factory(),
            'jenis' => fake()->randomElement(['izin', 'cuti']),
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => (clone $tanggalMulai)
                ->modify('+'.fake()->numberBetween(1, 5).' days')
                ->format('Y-m-d'),
            'alasan' => fake()->sentence(),
            'status' => $status,
            'approver_id' => null,
            'tanggal_pengajuan' => $tanggalPengajuan->format('Y-m-d H:i:s'),
            'tanggal_keputusan' => $status === 'menunggu'
                ? null
                : (clone $tanggalPengajuan)->modify('+2 days')->format('Y-m-d H:i:s'),
        ];
    }
}
