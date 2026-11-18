<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\AuditLog;
use App\Models\Cuti;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\Penggajian;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainFactoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_factories_create_records_with_valid_required_relations(): void
    {
        $jabatan = Jabatan::factory()->create();
        $shift = Shift::factory()->create();
        $karyawan = Karyawan::factory()->for($jabatan)->create();
        $user = User::factory()->create();
        $absensi = Absensi::factory()->for($karyawan)->create();
        $cuti = Cuti::factory()->for($karyawan)->create();
        $lembur = Lembur::factory()->for($karyawan)->create();
        $penggajian = Penggajian::factory()->for($karyawan)->create();
        $auditLog = AuditLog::factory()->for($user)->create();

        $this->assertModelExists($jabatan);
        $this->assertModelExists($shift);
        $this->assertModelExists($karyawan);
        $this->assertModelExists($absensi);
        $this->assertModelExists($cuti);
        $this->assertModelExists($lembur);
        $this->assertModelExists($penggajian);
        $this->assertModelExists($auditLog);
        $this->assertSame($jabatan->id, $karyawan->jabatan_id);
        $this->assertSame($karyawan->id, $absensi->karyawan_id);
        $this->assertSame($karyawan->id, $cuti->karyawan_id);
        $this->assertSame($karyawan->id, $lembur->karyawan_id);
        $this->assertSame($karyawan->id, $penggajian->karyawan_id);
        $this->assertSame($user->id, $auditLog->user_id);
        $this->assertGreaterThanOrEqual(1, $shift->id);
        $this->assertGreaterThanOrEqual(0, $penggajian->gaji_bersih);
    }
}
