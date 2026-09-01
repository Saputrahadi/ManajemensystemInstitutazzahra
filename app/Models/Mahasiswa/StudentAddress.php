<?php

namespace App\Models\Mahasiswa;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAddress extends Model
{
    use Auditable;

    protected $fillable = [
        'student_id',
        'address_type',
        'address',
        'province',
        'city',
        'district',
        'postal_code',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
