<?php

namespace App\Models\HasilAkademik;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use App\Models\Nilai\GradeFinalization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IP satu mahasiswa untuk satu tahun akademik + term, dihitung otomatis dari
 * grade_finalizations di periode itu (lihat `booted()`). Menyimpan/menghapus
 * baris ini memicu StudentCumulativeResult::recalculateForStudent() lewat
 * event, mengikuti pola KrsDetail -> Krs::recalculateTotalCredits().
 */
class StudentSemesterResult extends Model
{
    use Auditable;

    protected $fillable = ['student_id', 'academic_year', 'term', 'credits_taken', 'credits_earned', 'semester_gpa'];

    protected function casts(): array
    {
        return [
            'semester_gpa' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (StudentSemesterResult $result) {
            if (is_null($result->credits_taken) || is_null($result->credits_earned) || is_null($result->semester_gpa)) {
                $computed = static::computeForPeriod($result->student_id, $result->academic_year, $result->term);
                $result->credits_taken ??= $computed['credits_taken'];
                $result->credits_earned ??= $computed['credits_earned'];
                $result->semester_gpa ??= (string) $computed['semester_gpa'];
            }
        });

        static::saved(fn (StudentSemesterResult $result) => StudentCumulativeResult::recalculateForStudent($result->student_id));
        static::deleted(fn (StudentSemesterResult $result) => StudentCumulativeResult::recalculateForStudent($result->student_id));
    }

    public static function computeForPeriod(int $studentId, string $academicYear, string $term): array
    {
        $finalizations = GradeFinalization::where('student_id', $studentId)
            ->whereHas('courseClass.courseOffering', function ($query) use ($academicYear, $term) {
                $query->where('academic_year', $academicYear)->where('term', $term);
            })
            ->with('courseClass.courseOffering.course')
            ->get();

        $creditsTaken = 0;
        $creditsEarned = 0;
        $qualityPoints = 0.0;

        foreach ($finalizations as $finalization) {
            $credits = $finalization->courseClass->courseOffering->course->credits;
            $creditsTaken += $credits;
            // getRawOriginal(), bukan accessor bercast decimal: SQLite mengembalikan
            // kolom decimal sebagai float PHP mentah, bukan string — accessor bercast
            // di Eloquent men-forward float itu ke brick/math, yang sudah men-deprecate
            // menerima float (lihat catatan di docs/hasil-akademik.md).
            $gradePoint = (float) $finalization->getRawOriginal('grade_point');
            $qualityPoints += $credits * $gradePoint;

            if ($gradePoint > 0) {
                $creditsEarned += $credits;
            }
        }

        return [
            'credits_taken' => $creditsTaken,
            'credits_earned' => $creditsEarned,
            'semester_gpa' => $creditsTaken > 0 ? round($qualityPoints / $creditsTaken, 2) : 0.0,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
