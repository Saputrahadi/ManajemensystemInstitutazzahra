<?php

namespace App\Models\HasilAkademik;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use App\Models\Nilai\GradeFinalization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * IPK kumulatif — satu baris per mahasiswa. Dihitung ulang dari SEMUA
 * grade_finalizations mahasiswa (bukan dirata-rata dari semester_gpa per
 * semester, supaya bobot SKS tiap mata kuliah tetap konsisten), lewat
 * `recalculateForStudent()` yang dipanggil StudentSemesterResult::booted().
 */
class StudentCumulativeResult extends Model
{
    use Auditable;

    protected $fillable = ['student_id', 'total_credits_earned', 'cumulative_gpa'];

    protected function casts(): array
    {
        return [
            'cumulative_gpa' => 'decimal:2',
        ];
    }

    public static function recalculateForStudent(int $studentId): self
    {
        $finalizations = GradeFinalization::where('student_id', $studentId)
            ->with('courseClass.courseOffering.course')
            ->get();

        $creditsTaken = 0;
        $creditsEarned = 0;
        $qualityPoints = 0.0;

        foreach ($finalizations as $finalization) {
            $credits = $finalization->courseClass->courseOffering->course->credits;
            $creditsTaken += $credits;
            // getRawOriginal(), bukan accessor bercast decimal — lihat catatan di StudentSemesterResult::computeForPeriod().
            $gradePoint = (float) $finalization->getRawOriginal('grade_point');
            $qualityPoints += $credits * $gradePoint;

            if ($gradePoint > 0) {
                $creditsEarned += $credits;
            }
        }

        // IPK = total angka mutu / total SKS DIAMBIL (termasuk yang gagal), bukan cuma yang lulus.
        $gpa = $creditsTaken > 0 ? round($qualityPoints / $creditsTaken, 2) : 0.0;

        return static::updateOrCreate(
            ['student_id' => $studentId],
            ['total_credits_earned' => $creditsEarned, 'cumulative_gpa' => (string) $gpa]
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
