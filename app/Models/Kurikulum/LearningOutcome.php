<?php

namespace App\Models\Kurikulum;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** CPL (Capaian Pembelajaran Lulusan). */
class LearningOutcome extends Model
{
    use Auditable;

    protected $fillable = ['study_program_id', 'code', 'description'];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function courseLearningOutcomes(): HasMany
    {
        return $this->hasMany(CourseLearningOutcome::class);
    }
}
