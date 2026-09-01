<?php

namespace App\Models\Dosen;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturerAssignment extends Model
{
    use Auditable;

    protected $fillable = ['lecturer_id', 'study_program_id', 'assignment_type', 'start_date', 'end_date', 'status'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }
}
