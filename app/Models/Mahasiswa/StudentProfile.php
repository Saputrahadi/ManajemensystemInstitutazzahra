<?php

namespace App\Models\Mahasiswa;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use Auditable;

    protected $fillable = [
        'student_id',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'blood_type',
        'phone',
        'email',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
