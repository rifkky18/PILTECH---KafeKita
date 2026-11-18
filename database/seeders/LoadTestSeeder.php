<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class LoadTestSeeder extends Seeder
{
    private const EMPLOYEE_COUNT = 1000;

    private const BATCH_SIZE = 500;

    private const SCHEDULE_DAYS = 40;

    private const ATTENDANCE_DAYS = 35;

    private const LEAVE_COUNT = 5000;

    private const OVERTIME_COUNT = 10000;

    private const PAYROLL_PERIODS = 10;

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Load-test data may only be seeded in local or testing environments.');
        }

        $jobIds = $this->seedMasterData();
        $employeeNiks = $this->employeeNiks();
        $this->seedEmployees($employeeNiks, $jobIds);

        $employeeIdsByNik = DB::table('karyawan')
            ->whereIn('nik', $employeeNiks)
            ->pluck('id', 'nik')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
        $employeeIds = array_map(
            fn (string $nik): int => $employeeIdsByNik[$nik]
                ?? throw new RuntimeException("Load-test employee {$nik} could not be loaded."),
            $employeeNiks,
        );

        [$employeeUserIds, $approverId] = $this->seedUsers($employeeNiks);
        $shiftIds = DB::table('shift')
            ->where('nama_shift', 'like', 'Dummy Shift %')
            ->orderBy('nama_shift')
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
        $salaries = DB::table('jabatan')
            ->whereIn('id', $jobIds)
            ->orderBy('id')
            ->pluck('gaji_pokok')
            ->map(fn (mixed $salary): int => (int) $salary)
            ->all();

        if (count($shiftIds) !== 5 || count($salaries) !== 10 || count($employeeUserIds) !== self::EMPLOYEE_COUNT) {
            throw new RuntimeException(sprintf(
                'Load-test master records are incomplete (shifts: %d/5, salaries: %d/10, users: %d/1000).',
                count($shiftIds),
                count($salaries),
                count($employeeUserIds),
            ));
        }

        $this->seedSchedules($employeeIds, $shiftIds);
        $this->seedAttendance($employeeIds);
        $this->seedLeave($employeeIds, $approverId);
        $this->seedOvertime($employeeIds, $approverId);
        $this->seedPayroll($employeeIds, $salaries);
    }

    /**
     * @return list<int>
     */
    private function seedMasterData(): array
    {
        $jobRows = [];

        for ($number = 1; $number <= 10; $number++) {
            $jobRows[] = [
                'nama_jabatan' => sprintf('Dummy Jabatan %02d', $number),
                'gaji_pokok' => 3500000 + ($number * 500000),
            ];
        }

        $this->insertRows('jabatan', $jobRows);

        $shiftRows = [
            ['nama_shift' => 'Dummy Shift 01', 'jam_masuk' => '06:00:00', 'jam_pulang' => '14:00:00'],
            ['nama_shift' => 'Dummy Shift 02', 'jam_masuk' => '08:00:00', 'jam_pulang' => '16:00:00'],
            ['nama_shift' => 'Dummy Shift 03', 'jam_masuk' => '10:00:00', 'jam_pulang' => '18:00:00'],
            ['nama_shift' => 'Dummy Shift 04', 'jam_masuk' => '14:00:00', 'jam_pulang' => '22:00:00'],
            ['nama_shift' => 'Dummy Shift 05', 'jam_masuk' => '15:00:00', 'jam_pulang' => '23:00:00'],
        ];
        $shiftNames = array_column($shiftRows, 'nama_shift');
        $existingShiftNames = DB::table('shift')
            ->whereIn('nama_shift', $shiftNames)
            ->pluck('nama_shift')
            ->all();
        $missingShiftRows = array_values(array_filter(
            $shiftRows,
            fn (array $row): bool => ! in_array($row['nama_shift'], $existingShiftNames, true),
        ));
        $this->insertRows('shift', $missingShiftRows);

        $jobNames = array_column($jobRows, 'nama_jabatan');
        $jobIdsByName = DB::table('jabatan')
            ->whereIn('nama_jabatan', $jobNames)
            ->pluck('id', 'nama_jabatan')
            ->all();

        if (count($jobIdsByName) !== 10) {
            throw new RuntimeException('Could not load all 10 load-test job titles.');
        }

        return array_map(
            fn (string $name): int => (int) $jobIdsByName[$name],
            $jobNames,
        );
    }

    /**
     * @return list<string>
     */
    private function employeeNiks(): array
    {
        return array_map(
            static fn (int $number): string => sprintf('88888888%08d', $number),
            range(1, self::EMPLOYEE_COUNT),
        );
    }

    /**
     * @param  list<string>  $employeeNiks
     * @param  list<int>  $jobIds
     */
    private function seedEmployees(array $employeeNiks, array $jobIds): void
    {
        $rows = [];

        foreach ($employeeNiks as $index => $nik) {
            $rows[] = [
                'nik' => $nik,
                'nama' => sprintf('Dummy Karyawan %04d', $index + 1),
                'no_hp' => sprintf('08%010d', $index + 1),
                'alamat' => sprintf('Alamat dummy nomor %04d', $index + 1),
                'tanggal_masuk' => CarbonImmutable::parse('2020-01-01')
                    ->addDays($index % 2000)
                    ->toDateString(),
                'jabatan_id' => $jobIds[$index % count($jobIds)],
                'status' => 'aktif',
            ];
        }

        $this->insertRows('karyawan', $rows);
    }

    /**
     * @param  list<string>  $employeeNiks
     * @return array{0: list<int>, 1: int}
     */
    private function seedUsers(array $employeeNiks): array
    {
        $passwordHash = Hash::make(Str::random(64));
        $now = now();
        $rows = [
            [
                'name' => 'Dummy Load Test HR',
                'email' => 'loadtest.hr@example.test',
                'email_verified_at' => $now,
                'password' => $passwordHash,
                'password_hash' => $passwordHash,
                'nik' => null,
                'role' => 'admin_hr',
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Dummy Load Test Owner',
                'email' => 'loadtest.owner@example.test',
                'email_verified_at' => $now,
                'password' => $passwordHash,
                'password_hash' => $passwordHash,
                'nik' => null,
                'role' => 'pemilik',
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($employeeNiks as $index => $nik) {
            $number = $index + 1;
            $rows[] = [
                'name' => sprintf('Dummy Karyawan %04d', $number),
                'email' => sprintf('loadtest.%04d@example.test', $number),
                'email_verified_at' => $now,
                'password' => $passwordHash,
                'password_hash' => $passwordHash,
                'nik' => $nik,
                'role' => 'karyawan',
                'status' => 'aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->insertRows('users', $rows);

        $employeeUserIds = DB::table('users')
            ->whereIn('nik', $employeeNiks)
            ->pluck('id', 'nik')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
        $approverId = DB::table('users')
            ->where('email', 'loadtest.hr@example.test')
            ->value('id');

        if (count($employeeUserIds) !== self::EMPLOYEE_COUNT || $approverId === null) {
            throw new RuntimeException('Could not load all load-test user accounts.');
        }

        return [
            array_values($employeeUserIds),
            (int) $approverId,
        ];
    }

    /**
     * @param  list<int>  $employeeIds
     * @param  list<int>  $shiftIds
     */
    private function seedSchedules(array $employeeIds, array $shiftIds): void
    {
        $startDate = CarbonImmutable::parse('2024-01-01');
        $rows = (static function () use ($employeeIds, $shiftIds, $startDate): \Generator {
            foreach ($employeeIds as $employeeIndex => $employeeId) {
                for ($day = 0; $day < self::SCHEDULE_DAYS; $day++) {
                    yield [
                        'karyawan_id' => $employeeId,
                        'shift_id' => $shiftIds[($employeeIndex + $day) % count($shiftIds)],
                        'tanggal' => $startDate->addDays($day)->toDateString(),
                    ];
                }
            }
        })();

        $this->insertRows('jadwal', $rows);
    }

    /**
     * @param  list<int>  $employeeIds
     */
    private function seedAttendance(array $employeeIds): void
    {
        $startDate = CarbonImmutable::parse('2024-03-01');
        $rows = (static function () use ($employeeIds, $startDate): \Generator {
            foreach ($employeeIds as $employeeIndex => $employeeId) {
                for ($day = 0; $day < self::ATTENDANCE_DAYS; $day++) {
                    $recordNumber = ($employeeIndex * self::ATTENDANCE_DAYS) + $day;
                    $status = match ($recordNumber % 100) {
                        0, 1, 2 => 'alpa',
                        3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => 'terlambat',
                        default => 'hadir',
                    };

                    yield [
                        'karyawan_id' => $employeeId,
                        'tanggal' => $startDate->addDays($day)->toDateString(),
                        'jam_masuk' => $status === 'alpa' ? null : ($status === 'terlambat' ? '08:30:00' : '08:00:00'),
                        'jam_pulang' => $status === 'alpa' ? null : '16:00:00',
                        'status' => $status,
                    ];
                }
            }
        })();

        $this->insertRows('absensi', $rows);
    }

    /**
     * @param  list<int>  $employeeIds
     */
    private function seedLeave(array $employeeIds, int $approverId): void
    {
        $existingReasons = array_fill_keys(
            DB::table('cuti')
                ->where('alasan', 'like', '[LOADTEST:CUTI:%')
                ->pluck('alasan')
                ->all(),
            true,
        );
        $startDate = CarbonImmutable::parse('2024-05-01');
        $rows = (static function () use ($employeeIds, $approverId, $existingReasons, $startDate): \Generator {
            for ($sequence = 1; $sequence <= self::LEAVE_COUNT; $sequence++) {
                $marker = sprintf('[LOADTEST:CUTI:%05d]', $sequence);
                $reason = $marker.' Pengajuan cuti dummy untuk pengujian.';

                if (isset($existingReasons[$reason])) {
                    continue;
                }

                $employeeIndex = intdiv($sequence - 1, 5);
                $start = $startDate->addDays(($sequence * 3) % 365);
                $requestedAt = $start->subDays(4);
                $status = match ($sequence % 10) {
                    0, 1 => 'ditolak',
                    2, 3, 4, 5, 6, 7 => 'disetujui',
                    default => 'menunggu',
                };

                yield [
                    'karyawan_id' => $employeeIds[$employeeIndex],
                    'jenis' => $sequence % 2 === 0 ? 'cuti' : 'izin',
                    'tanggal_mulai' => $start->toDateString(),
                    'tanggal_selesai' => $start->addDays(($sequence % 3) + 1)->toDateString(),
                    'alasan' => $reason,
                    'status' => $status,
                    'approver_id' => $status === 'menunggu' ? null : $approverId,
                    'tanggal_pengajuan' => $requestedAt->toDateTimeString(),
                    'tanggal_keputusan' => $status === 'menunggu'
                        ? null
                        : $requestedAt->addDays(2)->toDateTimeString(),
                ];
            }
        })();

        $this->insertRows('cuti', $rows);
    }

    /**
     * @param  list<int>  $employeeIds
     */
    private function seedOvertime(array $employeeIds, int $approverId): void
    {
        $existingDescriptions = array_fill_keys(
            DB::table('lembur')
                ->where('keterangan', 'like', '[LOADTEST:LEMBUR:%')
                ->pluck('keterangan')
                ->all(),
            true,
        );
        $startDate = CarbonImmutable::parse('2024-07-01');
        $rows = (static function () use ($employeeIds, $approverId, $existingDescriptions, $startDate): \Generator {
            for ($sequence = 1; $sequence <= self::OVERTIME_COUNT; $sequence++) {
                $description = sprintf('[LOADTEST:LEMBUR:%05d] Lembur dummy untuk pengujian.', $sequence);

                if (isset($existingDescriptions[$description])) {
                    continue;
                }

                $employeeIndex = intdiv($sequence - 1, 10);
                $status = match ($sequence % 10) {
                    0, 1 => 'ditolak',
                    2, 3, 4, 5, 6, 7 => 'disetujui',
                    default => 'menunggu',
                };
                $decisionAt = $startDate->addDays((($sequence - 1) % 90) + 1)->setTime(18, 0);

                yield [
                    'karyawan_id' => $employeeIds[$employeeIndex],
                    'tanggal' => $startDate->addDays(($sequence - 1) % 90)->toDateString(),
                    'jumlah_jam' => number_format(1 + (($sequence % 40) / 10), 2, '.', ''),
                    'keterangan' => $description,
                    'status' => $status,
                    'approver_id' => $status === 'menunggu' ? null : $approverId,
                    'tanggal_keputusan' => $status === 'menunggu' ? null : $decisionAt->toDateTimeString(),
                ];
            }
        })();

        $this->insertRows('lembur', $rows);
    }

    /**
     * @param  list<int>  $employeeIds
     * @param  list<int>  $salaries
     */
    private function seedPayroll(array $employeeIds, array $salaries): void
    {
        $firstPeriod = CarbonImmutable::parse('2024-01-01');
        $rows = (static function () use ($employeeIds, $salaries, $firstPeriod): \Generator {
            foreach ($employeeIds as $employeeIndex => $employeeId) {
                for ($month = 0; $month < self::PAYROLL_PERIODS; $month++) {
                    $recordNumber = ($employeeIndex * self::PAYROLL_PERIODS) + $month + 1;
                    $salary = $salaries[$employeeIndex % count($salaries)];
                    $overtime = ($recordNumber % 6) * 150000;
                    $deduction = ($recordNumber % 4) * 50000;
                    $status = $month === self::PAYROLL_PERIODS - 1 ? 'draft' : 'final';

                    yield [
                        'karyawan_id' => $employeeId,
                        'periode' => $firstPeriod->addMonths($month)->format('Y-m'),
                        'gaji_pokok' => $salary,
                        'lembur' => $overtime,
                        'potongan' => $deduction,
                        'gaji_bersih' => $salary + $overtime - $deduction,
                        'status' => $status,
                        'finalized_at' => $status === 'final'
                            ? $firstPeriod->addMonths($month)->endOfMonth()->setTime(18, 0)->toDateTimeString()
                            : null,
                    ];
                }
            }
        })();

        $this->insertRows('penggajian', $rows);
    }

    /**
     * @param  iterable<array<string, mixed>>  $rows
     */
    private function insertRows(string $table, iterable $rows): void
    {
        $batch = [];

        foreach ($rows as $row) {
            $batch[] = $row;

            if (count($batch) === self::BATCH_SIZE) {
                DB::table($table)->insertOrIgnore($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            DB::table($table)->insertOrIgnore($batch);
        }
    }
}
