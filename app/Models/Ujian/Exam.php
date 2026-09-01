<?php

namespace App\Models\Ujian;

use App\Models\Core\Concerns\Auditable;
use App\Models\Kelas\CourseClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** UTS/UAS untuk satu Kelas. */
class Exam extends Model
{
    use Auditable;

    protected $fillable = ['class_id', 'type', 'title', 'exam_date', 'start_time', 'end_time', 'max_score', 'status'];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ExamParticipant::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ExamSubmission::class);
    }
}
