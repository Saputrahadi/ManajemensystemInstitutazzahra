<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan wajib: Unit sebelum Role (role scoping butuh Unit ada),
     * semua Permission (Core + tiap modul) sebelum Role (super_admin
     * mengambil '*' dari Permission yang sudah ada saat RoleSeeder jalan —
     * lihat RoleSeeder), Super Admin terakhir (butuh Unit + Role sudah ada).
     * AdmissionPeriodSeeder (data PMB) tidak digate ordering apa pun,
     * ditaruh di akhir.
     */
    public function run(): void
    {
        $this->call([
            UnitSeeder::class,
            PermissionSeeder::class,
            PmbPermissionSeeder::class,
            MahasiswaPermissionSeeder::class,
            DosenPermissionSeeder::class,
            KurikulumPermissionSeeder::class,
            KelasPermissionSeeder::class,
            KrsPermissionSeeder::class,
            PerkuliahanPermissionSeeder::class,
            UjianPermissionSeeder::class,
            NilaiPermissionSeeder::class,
            HasilAkademikPermissionSeeder::class,
            KeuanganPermissionSeeder::class,
            RoleSeeder::class,
            SuperAdminSeeder::class,
            AdmissionPeriodSeeder::class,
        ]);
    }
}
