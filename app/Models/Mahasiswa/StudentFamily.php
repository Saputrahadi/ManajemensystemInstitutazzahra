<?php

namespace App\Models\Mahasiswa;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFamily extends Model
{
    use Auditable;

    protected $fillable = [
        'student_id',
        'relation_type',
        'name',
        'occupation',
        'phone',
        'address',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
