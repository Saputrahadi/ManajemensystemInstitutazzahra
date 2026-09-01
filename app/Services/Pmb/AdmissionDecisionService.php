<?php

namespace App\Services\Pmb;

use App\Models\Pmb\AdmissionDecision;
use App\Models\Pmb\Applicant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Keputusan akhir seleksi — lihat blueprint #alurbisnis (Status Workflow Umum: Approved/Rejected). */
class AdmissionDecisionService
{
    public function decide(Applicant $applicant, User $decidedBy, string $decision, ?string $notes = null): AdmissionDecision
    {
        return DB::transaction(function () use ($applicant, $decidedBy, $decision, $notes) {
            $record = AdmissionDecision::updateOrCreate(
                ['applicant_id' => $applicant->id],
                [
                    'decision' => $decision,
                    'decided_by' => $decidedBy->id,
                    'decided_at' => now(),
                    'notes' => $notes,
                ],
            );

            $applicant->update([
                'status' => match ($decision) {
                    'diterima' => 'lulus',
                    'ditolak' => 'tidak_lulus',
                    default => $applicant->status, // cadangan — status pendaftar tidak berubah dulu
                },
            ]);

            return $record;
        });
    }
}
