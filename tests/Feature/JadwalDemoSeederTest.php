<?php

namespace Tests\Feature;

use Database\Seeders\JadwalDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JadwalDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_creates_required_master_data_and_250_schedules(): void
    {
        $this->seed(JadwalDemoSeeder::class);

        $this->assertDatabaseHas('jabatan', [
            'nama_jabatan' => 'Contoh Barista',
        ]);
        $this->assertDatabaseHas('karyawan', [
            'nik' => '9999999999999999',
            'status' => 'aktif',
        ]);
        $this->assertDatabaseCount('shift', 2);
        $this->assertDatabaseCount('karyawan', 36);
        $this->assertDatabaseCount('jadwal', 250);

        $this->seed(JadwalDemoSeeder::class);

        $this->assertDatabaseCount('jadwal', 250);
    }

    public function test_demo_seeder_preserves_existing_non_demo_master_data(): void
    {
        DB::table('jabatan')->insert([
            'nama_jabatan' => 'Supervisor',
            'gaji_pokok' => 5000000,
        ]);

        $this->seed(JadwalDemoSeeder::class);

        $this->assertDatabaseHas('jabatan', [
            'nama_jabatan' => 'Supervisor',
            'gaji_pokok' => 5000000,
        ]);
    }
}
