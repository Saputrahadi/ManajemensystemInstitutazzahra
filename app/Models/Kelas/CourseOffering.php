<?php

namespace App\Models\Kelas;

use App\Models\Core\Concerns\Auditable;
use App\Models\Kurikulum\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Pembukaan satu Mata Kuliah pada satu tahun akademik + term (ganjil/genap). */
class CourseOffering extends Model
{
    use Auditable;

    protected $fillable = ['course_id', 'academic_year', 'term', 'quota', 'status'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }
}
