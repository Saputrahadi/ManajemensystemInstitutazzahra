<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class NilaiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['nilai.component.view', 'Lihat Komponen Nilai', 'Nilai'],
            ['nilai.component.manage', 'Kelola Komponen Nilai', 'Nilai'],
            ['nilai.finalization.view', 'Lihat Nilai Akhir', 'Nilai'],
            ['nilai.finalization.manage', 'Kelola Nilai Akhir', 'Nilai'],
            ['nilai.change.approve', 'Setujui/Tolak Perubahan Nilai', 'Nilai'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
