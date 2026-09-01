<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

/**
 * Permission Core System (modul "Core"). Modul lain (Akademik, Keuangan, dst.)
 * menambahkan permission masing-masing lewat seeder terpisah begitu modul itu
 * dibangun — daftar ini sengaja tidak mengantisipasi semuanya di Fase 0.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['core.user.view', 'Lihat Pengguna', 'Core'],
            ['core.user.update', 'Ubah Pengguna', 'Core'],
            ['core.user.delete', 'Hapus Pengguna', 'Core'],
            ['core.role.manage', 'Kelola Role & Permission', 'Core'],
            ['core.unit.view', 'Lihat Unit', 'Core'],
            ['core.unit.manage', 'Kelola Unit', 'Core'],
            ['core.audit_log.view', 'Lihat Audit Log', 'Core'],
            ['core.setting.manage', 'Kelola Pengaturan Sistem', 'Core'],
            ['core.document.manage', 'Kelola Dokumen', 'Core'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
