<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class PerkuliahanPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['perkuliahan.session.view', 'Lihat Pertemuan Kuliah', 'Perkuliahan'],
            ['perkuliahan.session.manage', 'Kelola Pertemuan Kuliah', 'Perkuliahan'],
            ['perkuliahan.assignment.view', 'Lihat Tugas', 'Perkuliahan'],
            ['perkuliahan.assignment.manage', 'Kelola Tugas', 'Perkuliahan'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
