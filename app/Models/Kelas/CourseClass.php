<?php

namespace App\Models\Kelas;

use App\Models\Core\Concerns\Auditable;
use App\Models\Dosen\Lecturer;
use App\Models\Nilai\GradeComponent;
use App\Models\Nilai\GradeFinalization;
use App\Models\Perkuliahan\Assignment;
use App\Models\Perkuliahan\ClassSession;
use App\Models\Ujian\Exam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Kelas/rombel — bernama `CourseClass` (bukan `Class`, kata cadangan PHP), tabel tetap `classes` sesuai blueprint. */
class CourseClass extends Model
{
    use Auditable;

    protected $table = 'classes';

    protected $fillable = ['course_offering_id', 'code', 'capacity', 'status'];

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function lecturers(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'class_lecturers', 'class_id', 'lecturer_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'class_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'class_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    public function gradeComponents(): HasMany
    {
        return $this->hasMany(GradeComponent::class, 'class_id');
    }

    public function gradeFinalizations(): HasMany
    {
        return $this->hasMany(GradeFinalization::class, 'class_id');
    }
}
