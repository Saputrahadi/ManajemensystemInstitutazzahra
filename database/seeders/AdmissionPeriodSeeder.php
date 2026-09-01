<?php

namespace Database\Seeders;

use App\Models\Pmb\AdmissionPath;
use App\Models\Pmb\AdmissionPeriod;
use Illuminate\Database\Seeder;

/** Data awal domain PMB — satu periode aktif + jalur pendaftaran dasar. */
class AdmissionPeriodSeeder extends Seeder
{
    public function run(): void
    {
        AdmissionPeriod::updateOrCreate(
            ['code' => 'pmb-2027-2028-gel1'],
            [
                'name' => 'PMB 2027/2028 Gelombang 1',
                'academic_year' => '2027/2028',
                'opens_at' => '2027-01-01',
                'closes_at' => '2027-06-30',
                'status' => 'aktif',
            ],
        );

        foreach ([
            ['reguler', 'Reguler', 'Jalur pendaftaran umum.'],
            ['beasiswa', 'Beasiswa', 'Jalur pendaftaran dengan bantuan biaya pendidikan.'],
            ['rpl', 'RPL', 'Jalur rekognisi pembelajaran lampau — lihat modul RPL terpisah.'],
        ] as [$code, $name, $description]) {
            AdmissionPath::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'description' => $description, 'is_active' => true],
            );
        }
    }
}
