<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class HasilAkademikPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['hasil_akademik.result.view', 'Lihat Hasil Akademik', 'Hasil Akademik'],
            ['hasil_akademik.result.manage', 'Kelola Hasil Akademik', 'Hasil Akademik'],
            ['hasil_akademik.transcript.manage', 'Terbitkan Transkrip', 'Hasil Akademik'],
            ['hasil_akademik.graduation.manage', 'Kelola Kelulusan', 'Hasil Akademik'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
