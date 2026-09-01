<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use App\Models\Core\Role;
use Illuminate\Database\Seeder;

/**
 * 12 role inti dari blueprint #pengguna (ringkas dari 29 tipe pengguna penuh —
 * role turunan seperti Admin PMB/Admin Keuangan ditambah lewat panel Filament
 * begitu modulnya masing-masing dibangun, memakai RoleResource yang sudah ada).
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['super_admin', 'Super Admin', true, ['*']],
            ['rektor', 'Rektor', false, ['core.audit_log.view', 'core.unit.view']],
            ['wr1', 'Wakil Rektor I', false, ['core.unit.view']],
            ['wr2', 'Wakil Rektor II', false, ['core.unit.view']],
            ['dekan_kaprodi', 'Dekan / Kaprodi', false, ['core.user.view', 'core.unit.view']],
            ['dosen', 'Dosen', false, []],
            ['mahasiswa', 'Mahasiswa', false, []],
            ['baak', 'BAAK', false, ['core.user.view', 'core.unit.view']],
            ['keuangan', 'Keuangan', false, ['core.user.view']],
            ['sdm', 'SDM', false, ['core.user.view', 'core.user.update']],
            ['operator', 'Operator', false, []],
            ['auditor_spi', 'Auditor / SPI', false, ['core.audit_log.view', 'core.unit.view', 'core.user.view']],
        ];

        foreach ($roles as [$code, $name, $isSystem, $permissionCodes]) {
            $role = Role::updateOrCreate(['code' => $code], ['name' => $name, 'is_system' => $isSystem]);

            $permissionCodes = $permissionCodes === ['*']
                ? Permission::pluck('id')
                : Permission::whereIn('code', $permissionCodes)->pluck('id');

            $role->permissions()->sync($permissionCodes);
        }
    }
}
