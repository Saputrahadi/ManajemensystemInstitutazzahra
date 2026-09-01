<?php

namespace Database\Seeders;

use App\Models\Core\Role;
use App\Models\Core\Unit;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $institut = Unit::where('code', 'institut')->first();

        $admin = User::updateOrCreate(
            ['email' => 'superadmin@az-zahra.ac.id'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make(env('SEED_SUPERADMIN_PASSWORD', 'ganti-password-ini')),
                'unit_id' => $institut?->id,
                'status' => 'aktif',
                'email_verified_at' => now(),
            ],
        );

        $role = Role::where('code', 'super_admin')->first();

        if ($role && ! $admin->roles()->where('role_id', $role->id)->wherePivotNull('unit_id')->exists()) {
            // unit_id null => berlaku institut-wide, sesuai hasInstitutionWideRole().
            $admin->roles()->attach($role->id, ['unit_id' => null, 'assigned_at' => now()]);
        }

        if ($admin->notifications()->doesntExist()) {
            $admin->notify(new GenericNotification(
                'Selamat datang di Az-Zahra Digital Campus',
                'Core System Fase 0 sudah aktif — Login, RBAC, Unit, dan Audit Log siap dipakai.',
            ));
        }

        $this->command?->warn(
            'Super Admin dibuat: superadmin@az-zahra.ac.id — SEGERA ganti password default '.
            '(set SEED_SUPERADMIN_PASSWORD di .env sebelum seeding di production).'
        );
    }
}
