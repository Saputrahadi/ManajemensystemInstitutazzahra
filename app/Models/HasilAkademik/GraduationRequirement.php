<?php

namespace App\Models\HasilAkademik;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Syarat kelulusan per Program Studi — satu set syarat aktif per prodi. */
class GraduationRequirement extends Model
{
    use Auditable;

    protected $fillable = ['study_program_id', 'min_credits', 'min_gpa', 'max_study_years', 'notes'];

    protected function casts(): array
    {
        return [
            'min_gpa' => 'decimal:2',
        ];
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }
}
