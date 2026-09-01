<?php

namespace App\Models\Nilai;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Nilai mentah mahasiswa per komponen — belum dikalikan bobot. */
class Grade extends Model
{
    use Auditable;

    protected $fillable = ['grade_component_id', 'student_id', 'score'];

    public function gradeComponent(): BelongsTo
    {
        return $this->belongsTo(GradeComponent::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
