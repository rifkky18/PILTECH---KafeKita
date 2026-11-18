<?php

namespace Tests\Feature;

use Database\Seeders\LoadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoadTestSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_exactly_one_hundred_thousand_operational_records_and_is_repeatable(): void
    {
        $jabatanId = DB::table('jabatan')->insertGetId([
            'nama_jabatan' => 'Existing Employee Role',
            'gaji_pokok' => 4000000,
        ]);
        $existingKaryawanId = DB::table('karyawan')->insertGetId([
            'nik' => '7777777777777777',
            'nama' => 'Existing Employee',
            'tanggal_masuk' => '2024-01-01',
            'jabatan_id' => $jabatanId,
            'status' => 'aktif',
        ]);
        $existingShiftId = DB::table('shift')->insertGetId([
            'nama_shift' => 'Existing Shift',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '16:00:00',
        ]);
        DB::table('jadwal')->insert([
            'karyawan_id' => $existingKaryawanId,
            'shift_id' => $existingShiftId,
            'tanggal' => '2024-01-01',
        ]);

        $this->seed(LoadTestSeeder::class);

        $loadTestEmployeeIds = DB::table('karyawan')
            ->where('nik', 'like', '88888888%')
            ->pluck('id');

        $this->assertDatabaseHas('jadwal', [
            'karyawan_id' => $existingKaryawanId,
            'tanggal' => '2024-01-01',
        ]);
        $this->assertSame(1000, DB::table('karyawan')->where('nik', 'like', '88888888%')->where('status', 'aktif')->count());
        $this->assertSame(40000, DB::table('jadwal')->whereIn('karyawan_id', $loadTestEmployeeIds)->count());
        $this->assertSame(35000, DB::table('absensi')->whereIn('karyawan_id', $loadTestEmployeeIds)->count());
        $this->assertSame(5000, DB::table('cuti')->where('alasan', 'like', '[LOADTEST:CUTI:%')->count());
        $this->assertSame(10000, DB::table('lembur')->where('keterangan', 'like', '[LOADTEST:LEMBUR:%')->count());
        $this->assertSame(10000, DB::table('penggajian')->whereIn('karyawan_id', $loadTestEmployeeIds)->count());
        $this->assertSame(1000, DB::table('users')->where('email', 'like', 'loadtest.____@example.test')->count());

        $this->seed(LoadTestSeeder::class);

        $this->assertSame(40000, DB::table('jadwal')->whereIn('karyawan_id', $loadTestEmployeeIds)->count());
        $this->assertSame(35000, DB::table('absensi')->whereIn('karyawan_id', $loadTestEmployeeIds)->count());
        $this->assertSame(5000, DB::table('cuti')->where('alasan', 'like', '[LOADTEST:CUTI:%')->count());
        $this->assertSame(10000, DB::table('lembur')->where('keterangan', 'like', '[LOADTEST:LEMBUR:%')->count());
        $this->assertSame(10000, DB::table('penggajian')->whereIn('karyawan_id', $loadTestEmployeeIds)->count());
    }

    public function test_seeder_refuses_to_run_outside_local_and_testing_environments(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('only be seeded in local or testing environments');

        (new LoadTestSeeder)->run();
    }
}
