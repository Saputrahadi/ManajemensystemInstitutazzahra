<?php

namespace App\Models\Kurikulum;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** CPMK — pemetaan CPL ke Mata Kuliah tertentu. */
class CourseLearningOutcome extends Model
{
    use Auditable;

    protected $fillable = ['course_id', 'learning_outcome_id', 'code', 'description'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function learningOutcome(): BelongsTo
    {
        return $this->belongsTo(LearningOutcome::class);
    }
}
