<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class KurikulumPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['kurikulum.curriculum.view', 'Lihat Kurikulum', 'Kurikulum'],
            ['kurikulum.curriculum.manage', 'Kelola Kurikulum', 'Kurikulum'],
            ['kurikulum.course.view', 'Lihat Mata Kuliah', 'Kurikulum'],
            ['kurikulum.course.manage', 'Kelola Mata Kuliah', 'Kurikulum'],
            ['kurikulum.outcome.view', 'Lihat CPL', 'Kurikulum'],
            ['kurikulum.outcome.manage', 'Kelola CPL', 'Kurikulum'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
