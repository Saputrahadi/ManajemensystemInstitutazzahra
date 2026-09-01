<?php

namespace App\Models\Dosen;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use App\Models\Kelas\CourseClass;
use App\Models\Mahasiswa\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecturer extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nidn_nidk',
        'study_program_id',
        'academic_rank',
        'employment_status',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(LecturerProfile::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(LecturerEducation::class);
    }

    public function expertises(): HasMany
    {
        return $this->hasMany(LecturerExpertise::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LecturerAssignment::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(CourseClass::class, 'class_lecturers', 'lecturer_id', 'class_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function advisees(): HasMany
    {
        return $this->hasMany(Student::class, 'advisor_id');
    }

    public function advisings(): HasMany
    {
        return $this->hasMany(AcademicAdvising::class)->orderByDesc('advising_date');
    }
}
