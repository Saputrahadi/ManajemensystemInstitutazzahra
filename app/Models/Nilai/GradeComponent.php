<?php

namespace App\Models\Nilai;

use App\Models\Core\Concerns\Auditable;
use App\Models\Kelas\CourseClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Komponen penilaian per Kelas (mis. Tugas 30%, UTS 30%, UAS 40%). */
class GradeComponent extends Model
{
    use Auditable;

    protected $fillable = ['class_id', 'name', 'weight'];

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
