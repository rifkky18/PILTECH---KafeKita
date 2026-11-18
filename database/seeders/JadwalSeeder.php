<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Shift;
use Illuminate\Database\Seeder;
use RuntimeException;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $activeKaryawan = Karyawan::query()
            ->where('status', 'aktif')
            ->orderBy('id')
            ->get(['id']);
        $shiftIds = Shift::query()->orderBy('id')->pluck('id');

        if ($activeKaryawan->isEmpty()) {
            throw new RuntimeException('Cannot seed jadwal because no active karyawan exists.');
        }

        if ($shiftIds->isEmpty()) {
            throw new RuntimeException('Cannot seed jadwal because no shift exists.');
        }

        foreach ($activeKaryawan as $karyawanIndex => $karyawan) {
            for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                $date = today()->addDays($dayOffset)->toDateString();

                $scheduleExists = Jadwal::query()
                    ->where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $date)
                    ->exists();

                if (! $scheduleExists) {
                    Jadwal::query()->create([
                        'karyawan_id' => $karyawan->id,
                        'shift_id' => $shiftIds[($karyawanIndex + $dayOffset) % $shiftIds->count()],
                        'tanggal' => $date,
                    ]);
                }
            }
        }
    }
}
