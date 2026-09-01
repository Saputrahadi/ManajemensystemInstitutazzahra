<?php

namespace App\Models\Nilai;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Permintaan koreksi nilai atas satu GradeFinalization yang sudah terbit. */
class GradeChangeRequest extends Model
{
    use Auditable;

    protected $fillable = ['grade_finalization_id', 'requested_by', 'reason', 'requested_score', 'requested_letter_grade', 'status'];

    protected function casts(): array
    {
        return [
            'requested_score' => 'decimal:2',
        ];
    }

    public function gradeFinalization(): BelongsTo
    {
        return $this->belongsTo(GradeFinalization::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(GradeChangeApproval::class)->latest('decided_at');
    }
}
