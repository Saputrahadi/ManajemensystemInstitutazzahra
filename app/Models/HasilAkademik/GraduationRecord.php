<?php

namespace App\Models\HasilAkademik;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Catatan kelulusan resmi — `final_gpa` di-snapshot dari StudentCumulativeResult kalau tidak diisi manual. */
class GraduationRecord extends Model
{
    use Auditable;

    protected $fillable = ['student_id', 'graduation_date', 'final_gpa', 'thesis_title', 'graduation_number', 'status'];

    protected function casts(): array
    {
        return [
            'graduation_date' => 'date',
            'final_gpa' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (GraduationRecord $record) {
            if (is_null($record->final_gpa)) {
                $cumulative = StudentCumulativeResult::firstWhere('student_id', $record->student_id);
                // getRawOriginal(), bukan accessor bercast decimal — lihat catatan di StudentSemesterResult::computeForPeriod().
                $record->final_gpa = $cumulative ? (string) $cumulative->getRawOriginal('cumulative_gpa') : '0.00';
            }
        });

        static::created(function (GraduationRecord $record) {
            $record->student->update(['student_status' => 'lulus']);
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
