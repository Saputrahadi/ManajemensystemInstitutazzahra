<?php

namespace App\Models\Perkuliahan;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use Auditable;

    protected $fillable = ['class_session_id', 'student_id', 'status', 'notes', 'recorded_at'];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
