<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class KrsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['krs.view', 'Lihat KRS', 'KRS'],
            ['krs.manage', 'Kelola KRS', 'KRS'],
            ['krs.approve', 'Setujui/Tolak KRS', 'KRS'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
