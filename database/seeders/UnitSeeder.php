<?php

namespace Database\Seeders;

use App\Models\Core\Faculty;
use App\Models\Core\Institution;
use App\Models\Core\StudyProgram;
use App\Models\Core\Unit;
use Illuminate\Database\Seeder;

/**
 * 18 kategori unit dari blueprint #unit. Program Studi disini diisi dengan
 * 4 prodi awal (HKI, MBS, PAI, PBA per blueprint #database) sebagai anak dari
 * Fakultas — jadi total baris lebih dari 18 karena tiap Prodi jadi unit sendiri,
 * bukan satu baris generik "Program Studi".
 *
 * Baris institut/fakultas/prodi di `organizational_units` juga mendapat baris
 * pendamping di institutions/faculties/study_programs (lihat blueprint #erd)
 * supaya field khususnya (akreditasi, dekan, jenjang, dst.) tersedia sejak
 * awal — bukan cuma nama generik.
 */
class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $institutUnit = Unit::updateOrCreate(
            ['code' => 'institut'],
            ['name' => 'Institut Az-Zahra', 'type' => 'institut', 'parent_id' => null, 'order_index' => 0],
        );

        $institution = Institution::updateOrCreate(
            ['organizational_unit_id' => $institutUnit->id],
            [
                'code' => 'institut',
                'name' => 'Institut Az-Zahra',
                'short_name' => 'IAZ',
                'status' => 'aktif',
            ],
        );

        $fakultasUnit = Unit::updateOrCreate(
            ['code' => 'fakultas'],
            ['name' => 'Fakultas', 'type' => 'fakultas', 'parent_id' => $institutUnit->id, 'order_index' => 1],
        );

        $faculty = Faculty::updateOrCreate(
            ['organizational_unit_id' => $fakultasUnit->id],
            [
                'institution_id' => $institution->id,
                'code' => 'fakultas',
                'name' => 'Fakultas',
                'status' => 'aktif',
            ],
        );

        foreach ([
            ['HKI', 'Hukum Keluarga Islam'],
            ['MBS', 'Manajemen Bisnis Syariah'],
            ['PAI', 'Pendidikan Agama Islam'],
            ['PBA', 'Pendidikan Bahasa Arab'],
        ] as $i => [$code, $name]) {
            $prodiUnit = Unit::updateOrCreate(
                ['code' => 'prodi_'.strtolower($code)],
                ['name' => "Program Studi {$name}", 'type' => 'prodi', 'parent_id' => $fakultasUnit->id, 'order_index' => $i],
            );

            StudyProgram::updateOrCreate(
                ['organizational_unit_id' => $prodiUnit->id],
                [
                    'faculty_id' => $faculty->id,
                    'code' => strtolower($code),
                    'name' => "Program Studi {$name}",
                    'degree_level' => 'S1',
                    'status' => 'aktif',
                ],
            );
        }

        $lainnya = [
            ['biro_akademik', 'Biro/Bagian Akademik', 'biro'],
            ['biro_keuangan', 'Biro/Bagian Keuangan', 'biro'],
            ['biro_umum', 'Biro/Bagian Umum', 'biro'],
            ['biro_sdm', 'Biro/Bagian SDM', 'biro'],
            ['kemahasiswaan', 'Bagian Kemahasiswaan', 'bagian'],
            ['unit_ti', 'Pusat/Unit TI', 'unit'],
            ['perpustakaan', 'Perpustakaan', 'unit'],
            ['lpm', 'LPM', 'lembaga'],
            ['spi', 'SPI', 'lembaga'],
            ['penelitian_pengabdian', 'Penelitian & Pengabdian', 'lembaga'],
            ['kerja_sama', 'Kerja Sama & Hub. Kelembagaan', 'unit'],
            ['unit_pmb', 'Unit PMB', 'unit'],
            ['unit_rpl', 'Unit RPL', 'unit'],
            ['laboratorium', 'Laboratorium/Praktikum', 'unit'],
            ['unit_alumni', 'Unit Alumni', 'unit'],
        ];

        foreach ($lainnya as $i => [$code, $name, $type]) {
            Unit::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'type' => $type, 'parent_id' => $institutUnit->id, 'order_index' => 2 + $i],
            );
        }
    }
}
