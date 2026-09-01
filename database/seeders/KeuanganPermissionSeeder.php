<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use Illuminate\Database\Seeder;

class KeuanganPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['keuangan.fee_type.view', 'Lihat Jenis Tagihan', 'Keuangan'],
            ['keuangan.fee_type.manage', 'Kelola Jenis Tagihan', 'Keuangan'],
            ['keuangan.tariff.view', 'Lihat Master Tarif', 'Keuangan'],
            ['keuangan.tariff.manage', 'Kelola Master Tarif', 'Keuangan'],
            ['keuangan.invoice.view', 'Lihat Tagihan', 'Keuangan'],
            ['keuangan.invoice.manage', 'Kelola Tagihan', 'Keuangan'],
            ['keuangan.payment.view', 'Lihat Pembayaran', 'Keuangan'],
            ['keuangan.payment.verify', 'Verifikasi Pembayaran', 'Keuangan'],
            ['keuangan.extension.view', 'Lihat Perpanjangan Jatuh Tempo', 'Keuangan'],
            ['keuangan.extension.manage', 'Ajukan Perpanjangan Jatuh Tempo', 'Keuangan'],
            ['keuangan.extension.approve', 'Setujui/Tolak Perpanjangan Jatuh Tempo', 'Keuangan'],
        ];

        foreach ($permissions as [$code, $name, $module]) {
            Permission::updateOrCreate(['code' => $code], ['name' => $name, 'module' => $module]);
        }
    }
}
