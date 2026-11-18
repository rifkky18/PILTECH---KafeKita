<?php

namespace Tests\Feature;

use Database\Seeders\JadwalSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class JadwalSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_assigns_seven_days_to_active_employees_and_can_be_rerun(): void
    {
        $firstEmployeeId = $this->createEmployee('0000000000000001', 'aktif');
        $secondEmployeeId = $this->createEmployee('0000000000000002', 'aktif');
        $inactiveEmployeeId = $this->createEmployee('0000000000000003', 'nonaktif');
        $this->createShift('Pagi');
        $this->createShift('Malam');

        $this->seed(JadwalSeeder::class);

        $this->assertDatabaseCount('jadwal', 14);
        $this->assertDatabaseHas('jadwal', [
            'karyawan_id' => $firstEmployeeId,
            'tanggal' => today()->toDateTimeString(),
        ]);
        $this->assertDatabaseHas('jadwal', [
            'karyawan_id' => $secondEmployeeId,
            'tanggal' => today()->toDateTimeString(),
        ]);
        $this->assertDatabaseMissing('jadwal', [
            'karyawan_id' => $inactiveEmployeeId,
        ]);

        $this->seed(JadwalSeeder::class);

        $this->assertDatabaseCount('jadwal', 14);
    }

    public function test_seeder_reports_when_no_active_employee_exists(): void
    {
        $this->createShift('Pagi');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no active karyawan exists');

        (new JadwalSeeder)->run();
    }

    public function test_seeder_reports_when_no_shift_exists(): void
    {
        $this->createEmployee('0000000000000001', 'aktif');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no shift exists');

        (new JadwalSeeder)->run();
    }

    private function createEmployee(string $nik, string $status): int
    {
        $jabatanId = DB::table('jabatan')->insertGetId([
            'nama_jabatan' => 'Jabatan '.$nik,
            'gaji_pokok' => 3000000,
        ]);

        return DB::table('karyawan')->insertGetId([
            'nik' => $nik,
            'nama' => 'Karyawan '.$nik,
            'tanggal_masuk' => today()->toDateString(),
            'jabatan_id' => $jabatanId,
            'status' => $status,
        ]);
    }

    private function createShift(string $name): int
    {
        return DB::table('shift')->insertGetId([
            'nama_shift' => $name,
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '16:00:00',
        ]);
    }
}
