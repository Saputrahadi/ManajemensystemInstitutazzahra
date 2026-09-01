<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

/**
 * Permission domain PMB — ditambahkan lewat seeder terpisah dari Core
 * (lihat docblock PermissionSeeder), sesuai giliran modul ini dibangun.
 */
class PmbPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['pmb.period.manage', 'Kelola Periode & Jalur PMB', 'PMB'],
            ['pmb.applicant.view', 'Lihat Calon Mahasiswa', 'PMB'],
            ['pmb.applicant.manage', 'Kelola & Verifikasi Calon Mahasiswa', 'PMB'],
            ['pmb.decision.manage', 'Kelola Keputusan Seleksi', 'PMB'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
