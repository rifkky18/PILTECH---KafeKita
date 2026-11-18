<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Shift;
use Illuminate\Database\Seeder;
use RuntimeException;

class JadwalDemoSeeder extends Seeder
{
    private const SCHEDULE_TARGET = 250;

    public function run(): void
    {
        $scheduleCount = Jadwal::query()->count();

        if ($scheduleCount > self::SCHEDULE_TARGET) {
            throw new RuntimeException(
                "Cannot seed demo schedules because the jadwal table already contains {$scheduleCount} records; "
                .'it cannot be reduced to 250 without deleting existing data.',
            );
        }

        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama_jabatan' => 'Contoh Barista'],
            ['gaji_pokok' => 3000000],
        );

        $demoEmployeeNiks = array_merge(
            ['9999999999999999'],
            array_map(
                static fn (int $number): string => sprintf('999999999999%04d', $number),
                range(1, 35),
            ),
        );
        $demoKaryawan = collect($demoEmployeeNiks)
            ->values()
            ->map(function (string $nik, int $index) use ($jabatan): Karyawan {
                return Karyawan::query()->updateOrCreate(
                    ['nik' => $nik],
                    [
                        'nama' => 'Contoh Karyawan '.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                        'tanggal_masuk' => today(),
                        'jabatan_id' => $jabatan->id,
                        'status' => 'aktif',
                    ],
                );
            });

        Shift::query()->firstOrCreate(
            ['nama_shift' => 'Contoh Pagi'],
            [
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '16:00:00',
            ],
        );

        Shift::query()->firstOrCreate(
            ['nama_shift' => 'Contoh Sore'],
            [
                'jam_masuk' => '16:00:00',
                'jam_pulang' => '23:00:00',
            ],
        );

        $shiftIds = Shift::query()->orderBy('id')->pluck('id');

        foreach (range(0, 6) as $dayOffset) {
            foreach ($demoKaryawan as $karyawanIndex => $karyawan) {
                if ($scheduleCount >= self::SCHEDULE_TARGET) {
                    break 2;
                }

                $date = today()->addDays($dayOffset)->toDateString();
                $scheduleExists = Jadwal::query()
                    ->where('karyawan_id', $karyawan->id)
                    ->whereDate('tanggal', $date)
                    ->exists();

                if ($scheduleExists) {
                    continue;
                }

                Jadwal::query()->create([
                    'karyawan_id' => $karyawan->id,
                    'shift_id' => $shiftIds[($karyawanIndex + $dayOffset) % $shiftIds->count()],
                    'tanggal' => $date,
                ]);

                $scheduleCount++;
            }
        }

        if ($scheduleCount < self::SCHEDULE_TARGET) {
            throw new RuntimeException(
                "Could not create 250 jadwal records; only {$scheduleCount} unique employee-date combinations are available.",
            );
        }
    }
}
