<?php

namespace App\Models\Perkuliahan;

use App\Models\Core\Concerns\Auditable;
use App\Models\Kelas\CourseClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use Auditable;

    protected $fillable = ['class_id', 'title', 'description', 'due_date', 'max_score'];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
