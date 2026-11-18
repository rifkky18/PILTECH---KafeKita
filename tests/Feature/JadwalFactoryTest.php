<?php

namespace Tests\Feature;

use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Shift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_a_schedule_for_an_existing_active_employee_and_shift(): void
    {
        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => Jabatan::factory(),
            'status' => 'aktif',
        ]);
        $shift = Shift::factory()->create();

        $jadwal = Jadwal::factory()
            ->for($karyawan)
            ->for($shift)
            ->create();

        $this->assertModelExists($jadwal);
        $this->assertSame($karyawan->id, $jadwal->karyawan_id);
        $this->assertSame($shift->id, $jadwal->shift_id);
        $this->assertNotNull($jadwal->tanggal);
    }
}
