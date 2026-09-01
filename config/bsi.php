<?php

/*
|--------------------------------------------------------------------------
| Integrasi Bank BSI (blueprint modul "04. Integrasi Bank BSI")
|--------------------------------------------------------------------------
| Kredensial sandbox/production BELUM tersedia di environment dev ini —
| lihat docs/keuangan.md untuk penjelasan bagaimana alur VA/callback tetap
| diverifikasi end-to-end tanpa kredensial nyata (pola yang sama dengan
| BSI_* placeholder yang sudah direservasi di .env.example sejak Fase 0).
*/

return [
    'env' => env('BSI_ENV', 'sandbox'),
    'client_id' => env('BSI_CLIENT_ID'),
    'client_secret' => env('BSI_CLIENT_SECRET'),
    'partner_id' => env('BSI_PARTNER_ID'),
    'base_url' => env('BSI_BASE_URL', 'https://sandbox.bankbsi.co.id'),
    'callback_token' => env('BSI_CALLBACK_TOKEN'),

    // Kode perusahaan/company code BSI VA — bagian tetap di depan nomor VA.
    // Placeholder sampai kredensial sandbox nyata didaftarkan.
    'company_code' => env('BSI_COMPANY_CODE', '77777'),
];
