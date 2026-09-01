<?php

namespace App\Services\Pmb;

use App\Models\Pmb\AdmissionScore;
use App\Models\Pmb\Applicant;

/**
 * Rekap skor gabungan — rata-rata seluruh admission_test_results (bisa lebih
 * dari satu ujian per periode) dan interview_results milik Applicant.
 * Blueprint tidak menentukan bobot spesifik, jadi dipakai rata-rata polos;
 * sesuaikan di sini kalau kebijakan bobot resmi sudah ditetapkan.
 */
class AdmissionScoringService
{
    public function recompute(Applicant $applicant): AdmissionScore
    {
        $testScore = $applicant->testResults()->whereNotNull('score')->avg('score');
        $interviewScore = $applicant->interviews()
            ->whereHas('result')
            ->with('result')
            ->get()
            ->pluck('result.score')
            ->filter()
            ->avg();

        $components = array_filter([$testScore, $interviewScore], fn ($v) => $v !== null);
        $finalScore = count($components) > 0 ? array_sum($components) / count($components) : null;

        return AdmissionScore::updateOrCreate(
            ['applicant_id' => $applicant->id],
            [
                'test_score' => $testScore,
                'interview_score' => $interviewScore,
                'final_score' => $finalScore,
                'computed_at' => now(),
            ],
        );
    }
}
