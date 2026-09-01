<?php

namespace App\Models\Pmb;

use App\Models\Core\StudyProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NimCounter extends Model
{
    protected $fillable = ['study_program_id', 'academic_year', 'seq'];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }
}
