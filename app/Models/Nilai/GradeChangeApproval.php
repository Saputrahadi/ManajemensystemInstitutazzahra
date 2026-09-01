<?php

namespace App\Models\Nilai;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Keputusan atas satu GradeChangeRequest — riwayat, mengikuti pola KrsApproval.
 * Kalau disetujui, GradeFinalization induknya langsung ditimpa dengan nilai
 * yang diminta (skor + huruf mutu + angka mutu dari GradeFinalization::scoreToScale-nya
 * huruf yang diminta, bukan dihitung ulang dari komponen).
 */
class GradeChangeApproval extends Model
{
    use Auditable;

    protected $fillable = ['grade_change_request_id', 'approver_id', 'status', 'notes', 'decided_at'];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (GradeChangeApproval $approval) {
            $request = $approval->gradeChangeRequest;
            $request->update(['status' => $approval->status]);

            if ($approval->status === 'disetujui') {
                // getRawOriginal(), bukan accessor bercast decimal — lihat catatan di
                // App\Models\HasilAkademik\StudentSemesterResult::computeForPeriod().
                $request->gradeFinalization->update([
                    'final_score' => (string) $request->getRawOriginal('requested_score'),
                    'letter_grade' => $request->requested_letter_grade,
                    'grade_point' => GradeFinalization::pointForLetter($request->requested_letter_grade),
                ]);
            }
        });
    }

    public function gradeChangeRequest(): BelongsTo
    {
        return $this->belongsTo(GradeChangeRequest::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
