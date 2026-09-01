<?php

namespace App\Models\Nilai;

use App\Models\Core\Concerns\Auditable;
use App\Models\Kelas\CourseClass;
use App\Models\Mahasiswa\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Nilai akhir per mahasiswa per Kelas. `final_score`/`letter_grade`/`grade_point`
 * dihitung otomatis dari grade_components + grades saat baris ini dibuat
 * (lihat `booted()`), kecuali sudah diisi manual — koreksi setelahnya lewat
 * GradeChangeRequest/Approval, bukan edit langsung.
 */
class GradeFinalization extends Model
{
    use Auditable;

    protected $fillable = ['class_id', 'student_id', 'final_score', 'letter_grade', 'grade_point', 'finalized_by', 'finalized_at'];

    protected function casts(): array
    {
        return [
            'final_score' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'finalized_at' => 'datetime',
        ];
    }

    private const SCALE = [
        ['min' => 85, 'letter' => 'A', 'point' => 4.00],
        ['min' => 80, 'letter' => 'AB', 'point' => 3.50],
        ['min' => 75, 'letter' => 'B', 'point' => 3.00],
        ['min' => 70, 'letter' => 'BC', 'point' => 2.50],
        ['min' => 65, 'letter' => 'C', 'point' => 2.00],
        ['min' => 60, 'letter' => 'CD', 'point' => 1.50],
        ['min' => 55, 'letter' => 'D', 'point' => 1.00],
        ['min' => 0, 'letter' => 'E', 'point' => 0.00],
    ];

    protected static function booted(): void
    {
        static::creating(function (GradeFinalization $finalization) {
            if (is_null($finalization->final_score)) {
                $finalization->final_score = (string) static::computeWeightedScore($finalization->class_id, $finalization->student_id);
            }

            if (is_null($finalization->letter_grade) || is_null($finalization->grade_point)) {
                [$letter, $point] = static::scoreToScale((float) $finalization->final_score);
                $finalization->letter_grade ??= $letter;
                $finalization->grade_point ??= (string) $point;
            }

            $finalization->finalized_at ??= now();
        });
    }

    public static function computeWeightedScore(int $classId, int $studentId): float
    {
        $components = GradeComponent::where('class_id', $classId)->get();

        $weighted = 0.0;
        foreach ($components as $component) {
            $score = Grade::where('grade_component_id', $component->id)
                ->where('student_id', $studentId)
                ->value('score') ?? 0;

            $weighted += $score * ($component->weight / 100);
        }

        return round($weighted, 2);
    }

    public static function scoreToScale(float $score): array
    {
        foreach (self::SCALE as $row) {
            if ($score >= $row['min']) {
                return [$row['letter'], (string) $row['point']];
            }
        }

        return ['E', '0.00'];
    }

    public static function pointForLetter(string $letter): string
    {
        foreach (self::SCALE as $row) {
            if ($row['letter'] === $letter) {
                return (string) $row['point'];
            }
        }

        return '0.00';
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(GradeChangeRequest::class);
    }
}
