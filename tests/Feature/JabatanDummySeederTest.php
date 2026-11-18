<?php

namespace Tests\Feature;

use Database\Seeders\JabatanDummySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class JabatanDummySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_adds_one_hundred_thousand_jabatan_rows_and_is_repeatable(): void
    {
        DB::table('jabatan')->insert([
            'nama_jabatan' => 'Existing Role',
            'gaji_pokok' => 4000000,
        ]);

        $this->seed(JabatanDummySeeder::class);

        $this->assertSame(100000, DB::table('jabatan')->where('nama_jabatan', 'like', 'Dummy Jabatan Tambahan %')->count());
        $this->assertDatabaseHas('jabatan', [
            'nama_jabatan' => 'Existing Role',
            'gaji_pokok' => 4000000,
        ]);

        $this->seed(JabatanDummySeeder::class);

        $this->assertSame(100000, DB::table('jabatan')->where('nama_jabatan', 'like', 'Dummy Jabatan Tambahan %')->count());
        $this->assertDatabaseCount('jabatan', 100001);
    }

    public function test_seeder_refuses_to_run_outside_local_and_testing_environments(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('only be seeded in local or testing environments');

        (new JabatanDummySeeder)->run();
    }
}
