<?php

namespace App\Models\Dosen;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Bimbingan Akademik — catatan konsultasi Dosen Wali dengan mahasiswa bimbingannya. */
class AcademicAdvising extends Model
{
    use Auditable;

    protected $fillable = ['student_id', 'lecturer_id', 'advising_date', 'topic', 'notes'];

    protected function casts(): array
    {
        return [
            'advising_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
