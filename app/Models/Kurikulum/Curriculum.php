<?php

namespace App\Models\Kurikulum;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curriculum extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = ['study_program_id', 'code', 'name', 'academic_year', 'total_credits', 'status'];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'curriculum_courses')
            ->withPivot(['semester', 'is_mandatory'])
            ->withTimestamps();
    }
}
