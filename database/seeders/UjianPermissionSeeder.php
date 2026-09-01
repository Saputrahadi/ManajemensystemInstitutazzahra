<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class UjianPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['ujian.exam.view', 'Lihat Ujian', 'Ujian'],
            ['ujian.exam.manage', 'Kelola Ujian', 'Ujian'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
