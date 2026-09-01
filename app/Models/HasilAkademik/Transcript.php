<?php

namespace App\Models\HasilAkademik;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Transkrip yang diterbitkan — snapshot beku dari StudentCumulativeResult saat diterbitkan. */
class Transcript extends Model
{
    use Auditable;

    protected $fillable = ['student_id', 'cumulative_gpa_snapshot', 'total_credits_snapshot', 'file_path', 'issued_by', 'issued_at'];

    protected function casts(): array
    {
        return [
            'cumulative_gpa_snapshot' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Transcript $transcript) {
            if (is_null($transcript->cumulative_gpa_snapshot) || is_null($transcript->total_credits_snapshot)) {
                $cumulative = StudentCumulativeResult::firstWhere('student_id', $transcript->student_id);
                // getRawOriginal(), bukan accessor bercast decimal — lihat catatan di StudentSemesterResult::computeForPeriod().
                $transcript->cumulative_gpa_snapshot ??= $cumulative ? (string) $cumulative->getRawOriginal('cumulative_gpa') : '0.00';
                $transcript->total_credits_snapshot ??= $cumulative->total_credits_earned ?? 0;
            }

            $transcript->issued_at ??= now();
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
