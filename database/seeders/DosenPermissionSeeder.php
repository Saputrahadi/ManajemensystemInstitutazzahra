<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class DosenPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['dosen.lecturer.view', 'Lihat Dosen', 'Dosen'],
            ['dosen.lecturer.manage', 'Kelola Data Dosen', 'Dosen'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
