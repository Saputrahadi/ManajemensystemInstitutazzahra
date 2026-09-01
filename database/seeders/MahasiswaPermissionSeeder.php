<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

/** Permission domain Mahasiswa — pola sama dengan PmbPermissionSeeder. */
class MahasiswaPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['mahasiswa.student.view', 'Lihat Mahasiswa', 'Mahasiswa'],
            ['mahasiswa.student.manage', 'Kelola Data Mahasiswa', 'Mahasiswa'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
