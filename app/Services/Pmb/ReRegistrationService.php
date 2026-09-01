<?php

namespace App\Services\Pmb;

use App\Models\Pmb\Applicant;
use App\Models\Pmb\NimCounter;
use App\Models\Pmb\ReRegistration;
use Illuminate\Support\Facades\DB;

/**
 * Registrasi ulang & penerbitan NIM — langkah terakhir alur PMB (blueprint
 * #intimahasiswa). Hanya boleh dijalankan untuk Applicant dengan keputusan
 * "diterima" (status `lulus`).
 *
 * Format NIM: {2 digit awal tahun ajaran}{study_program_id 2 digit}{urut 4
 * digit} — meniru contoh NIM "27010001" di blueprint (Prodi PAI). Dijamin
 * unik per Prodi+tahun ajaran lewat NimCounter (row-locked), sama pola
 * dengan nomor pendaftaran.
 */
class ReRegistrationService
{
    public function register(Applicant $applicant): ReRegistration
    {
        if ($applicant->status !== 'lulus') {
            throw new \RuntimeException('Hanya Calon Mahasiswa dengan status lulus yang bisa registrasi ulang.');
        }

        return DB::transaction(function () use ($applicant) {
            $academicYear = $applicant->admissionPeriod->academic_year;
            $yearPrefix = substr($academicYear, 2, 2); // 2 digit terakhir tahun pertama, mis. "2027/2028" -> "27"

            $counter = NimCounter::where('study_program_id', $applicant->study_program_id)
                ->where('academic_year', $academicYear)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = NimCounter::create([
                    'study_program_id' => $applicant->study_program_id,
                    'academic_year' => $academicYear,
                    'seq' => 0,
                ]);
            }

            $counter->increment('seq');

            $nim = sprintf('%s%02d%04d', $yearPrefix, $applicant->study_program_id, $counter->seq);

            $reRegistration = ReRegistration::updateOrCreate(
                ['applicant_id' => $applicant->id],
                [
                    'status' => 'selesai',
                    'nim' => $nim,
                    'registered_at' => now(),
                ],
            );

            $applicant->update(['status' => 'selesai']);

            return $reRegistration;
        });
    }
}
