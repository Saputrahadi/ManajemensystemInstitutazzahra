<?php

namespace Tests\Feature\Core;

use App\Models\Core\Role;
use App\Models\Core\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Skenario kanonis dari blueprint Definition of Done Fase 0:
 * "Kaprodi Prodi A tidak bisa lihat data Prodi B".
 */
class RbacUnitScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_kaprodi_only_sees_users_in_own_prodi(): void
    {
        $institut = Unit::create(['code' => 'institut', 'name' => 'Institut', 'type' => 'institut']);
        $prodiA = Unit::create(['code' => 'prodi_a', 'name' => 'Prodi A', 'type' => 'prodi', 'parent_id' => $institut->id]);
        $prodiB = Unit::create(['code' => 'prodi_b', 'name' => 'Prodi B', 'type' => 'prodi', 'parent_id' => $institut->id]);

        $roleKaprodi = Role::create(['code' => 'dekan_kaprodi', 'name' => 'Kaprodi']);

        $kaprodiA = User::factory()->create(['unit_id' => $prodiA->id]);
        $kaprodiA->roles()->attach($roleKaprodi->id, ['unit_id' => $prodiA->id, 'assigned_at' => now()]);

        $dosenA = User::factory()->create(['unit_id' => $prodiA->id]);
        $dosenB = User::factory()->create(['unit_id' => $prodiB->id]);

        $visible = User::query()->visibleTo($kaprodiA)->pluck('id');

        $this->assertTrue($visible->contains($dosenA->id), 'Kaprodi Prodi A harus bisa lihat dosen di Prodi A sendiri.');
        $this->assertFalse($visible->contains($dosenB->id), 'Kaprodi Prodi A tidak boleh bisa lihat dosen di Prodi B.');
    }

    public function test_institution_wide_role_sees_all_units(): void
    {
        $institut = Unit::create(['code' => 'institut', 'name' => 'Institut', 'type' => 'institut']);
        $prodiA = Unit::create(['code' => 'prodi_a', 'name' => 'Prodi A', 'type' => 'prodi', 'parent_id' => $institut->id]);
        $prodiB = Unit::create(['code' => 'prodi_b', 'name' => 'Prodi B', 'type' => 'prodi', 'parent_id' => $institut->id]);

        $superAdminRole = Role::create(['code' => 'super_admin', 'name' => 'Super Admin', 'is_system' => true]);

        $superAdmin = User::factory()->create(['unit_id' => $institut->id]);
        $superAdmin->roles()->attach($superAdminRole->id, ['unit_id' => null, 'assigned_at' => now()]);

        $dosenA = User::factory()->create(['unit_id' => $prodiA->id]);
        $dosenB = User::factory()->create(['unit_id' => $prodiB->id]);

        $visible = User::query()->visibleTo($superAdmin)->pluck('id');

        $this->assertTrue($visible->contains($dosenA->id));
        $this->assertTrue($visible->contains($dosenB->id));
    }

    public function test_dean_scoped_to_faculty_also_sees_child_prodi(): void
    {
        $institut = Unit::create(['code' => 'institut', 'name' => 'Institut', 'type' => 'institut']);
        $fakultas = Unit::create(['code' => 'fakultas', 'name' => 'Fakultas', 'type' => 'fakultas', 'parent_id' => $institut->id]);
        $prodiA = Unit::create(['code' => 'prodi_a', 'name' => 'Prodi A', 'type' => 'prodi', 'parent_id' => $fakultas->id]);

        $roleDekan = Role::create(['code' => 'dekan_kaprodi', 'name' => 'Dekan']);

        $dekan = User::factory()->create(['unit_id' => $fakultas->id]);
        $dekan->roles()->attach($roleDekan->id, ['unit_id' => $fakultas->id, 'assigned_at' => now()]);

        $dosenProdiA = User::factory()->create(['unit_id' => $prodiA->id]);

        $visible = User::query()->visibleTo($dekan)->pluck('id');

        $this->assertTrue($visible->contains($dosenProdiA->id), 'Dekan yang di-scope ke Fakultas harus tetap bisa lihat dosen di Prodi turunannya.');
    }
}
