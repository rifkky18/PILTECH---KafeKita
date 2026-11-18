<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class JabatanDummySeeder extends Seeder
{
    private const ROW_COUNT = 100000;

    private const BATCH_SIZE = 500;

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Dummy jabatan data may only be seeded in local or testing environments.');
        }

        $batch = [];

        for ($number = 1; $number <= self::ROW_COUNT; $number++) {
            $batch[] = [
                'nama_jabatan' => sprintf('Dummy Jabatan Tambahan %06d', $number),
                'gaji_pokok' => 3000000 + (($number % 25) * 250000),
            ];

            if (count($batch) === self::BATCH_SIZE) {
                DB::table('jabatan')->insertOrIgnore($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            DB::table('jabatan')->insertOrIgnore($batch);
        }

        $seededCount = DB::table('jabatan')
            ->where('nama_jabatan', 'like', 'Dummy Jabatan Tambahan %')
            ->count();

        if ($seededCount !== self::ROW_COUNT) {
            throw new RuntimeException("Expected 100000 dummy jabatan rows; found {$seededCount}.");
        }

        $this->command?->info('Verified 100000 dummy jabatan rows.');
    }
}
