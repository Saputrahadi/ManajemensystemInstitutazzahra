<?php

namespace App\Models\Mahasiswa;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    use Auditable;

    protected $fillable = [
        'student_id',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'size',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
