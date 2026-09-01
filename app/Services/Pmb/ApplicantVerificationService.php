<?php

namespace App\Services\Pmb;

use App\Models\Pmb\Applicant;
use App\Models\Pmb\ApplicantVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Verifikasi administrasi Calon Mahasiswa — lihat blueprint #alurbisnis (Trigger → ... → Persetujuan). */
class ApplicantVerificationService
{
    public function verify(Applicant $applicant, User $verifier, string $status, ?string $notes = null): ApplicantVerification
    {
        return DB::transaction(function () use ($applicant, $verifier, $status, $notes) {
            $verification = ApplicantVerification::create([
                'applicant_id' => $applicant->id,
                'verified_by' => $verifier->id,
                'status' => $status,
                'notes' => $notes,
                'verified_at' => now(),
            ]);

            $applicant->update([
                'status' => match ($status) {
                    'disetujui' => 'terverifikasi',
                    'revisi' => 'menunggu_verifikasi',
                    default => $applicant->status,
                },
            ]);

            return $verification;
        });
    }
}
