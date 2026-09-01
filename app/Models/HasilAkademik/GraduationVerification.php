<?php

namespace App\Models\HasilAkademik;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Verifikasi pemenuhan syarat kelulusan seorang mahasiswa. `meets_requirements`
 * disarankan otomatis (bandingkan StudentCumulativeResult mahasiswa vs.
 * min_credits/min_gpa di GraduationRequirement) kalau tidak diisi manual,
 * tapi tetap bisa ditimpa penilaian verifikator (mis. kelonggaran khusus).
 */
class GraduationVerification extends Model
{
    use Auditable;

    protected $fillable = ['student_id', 'graduation_requirement_id', 'meets_requirements', 'notes', 'verified_by', 'verified_at'];

    protected function casts(): array
    {
        return [
            'meets_requirements' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (GraduationVerification $verification) {
            if (is_null($verification->meets_requirements)) {
                $cumulative = StudentCumulativeResult::firstWhere('student_id', $verification->student_id);
                $requirement = GraduationRequirement::find($verification->graduation_requirement_id);

                // getRawOriginal(), bukan accessor bercast decimal — lihat catatan di StudentSemesterResult::computeForPeriod().
                $verification->meets_requirements = $cumulative
                    && $requirement
                    && $cumulative->total_credits_earned >= $requirement->min_credits
                    && (float) $cumulative->getRawOriginal('cumulative_gpa') >= (float) $requirement->getRawOriginal('min_gpa');
            }

            $verification->verified_at ??= now();
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function graduationRequirement(): BelongsTo
    {
        return $this->belongsTo(GraduationRequirement::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
