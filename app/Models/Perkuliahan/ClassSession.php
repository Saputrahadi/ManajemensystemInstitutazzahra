<?php

namespace App\Models\Perkuliahan;

use App\Models\Core\Concerns\Auditable;
use App\Models\Kelas\CourseClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Satu pertemuan kuliah dari sebuah Kelas — bukan `Session` (bentrok dengan sesi HTTP), diberi nama `ClassSession`. */
class ClassSession extends Model
{
    use Auditable;

    protected $fillable = ['class_id', 'meeting_number', 'topic', 'session_date', 'status'];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(TeachingMaterial::class);
    }
}
