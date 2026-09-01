<?php

namespace App\Services\Pmb;

use App\Models\Pmb\AdmissionPeriod;
use App\Models\Pmb\Applicant;
use Illuminate\Support\Facades\DB;

/**
 * Pendaftaran Calon Mahasiswa baru — lihat blueprint #intimahasiswa
 * ("Website → PMB → Nomor Pendaftaran → ..."). Nomor pendaftaran dijamin
 * unik per periode lewat row-locking (`lockForUpdate`) di `admission_periods
 * .last_sequence`, sama pola dengan `SkCounter` pada project sistem-yayasan
 * yang lain — aman dari race condition kalau ada dua pendaftar submit
 * bersamaan.
 */
class ApplicantRegistrationService
{
    public function register(array $data): Applicant
    {
        return DB::transaction(function () use ($data) {
            $period = AdmissionPeriod::whereKey($data['admission_period_id'])->lockForUpdate()->first();

            $period->increment('last_sequence');

            $registrationNumber = sprintf(
                '%s-%s',
                strtoupper($period->code),
                str_pad((string) $period->last_sequence, 4, '0', STR_PAD_LEFT),
            );

            return Applicant::create([
                ...$data,
                'registration_number' => $registrationNumber,
                'status' => 'mendaftar',
                'registration_fee_status' => 'belum_bayar',
            ]);
        });
    }
}
