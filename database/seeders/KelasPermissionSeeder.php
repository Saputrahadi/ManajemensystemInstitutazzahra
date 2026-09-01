<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class KelasPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['kelas.offering.view', 'Lihat Pembukaan Mata Kuliah', 'Kelas & Jadwal'],
            ['kelas.offering.manage', 'Kelola Pembukaan Mata Kuliah', 'Kelas & Jadwal'],
            ['kelas.class.view', 'Lihat Kelas', 'Kelas & Jadwal'],
            ['kelas.class.manage', 'Kelola Kelas', 'Kelas & Jadwal'],
            ['kelas.room.view', 'Lihat Ruang', 'Kelas & Jadwal'],
            ['kelas.room.manage', 'Kelola Ruang', 'Kelas & Jadwal'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
