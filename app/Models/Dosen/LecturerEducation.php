<?php

namespace App\Models\Dosen;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturerEducation extends Model
{
    use Auditable;

    protected $fillable = ['lecturer_id', 'degree_level', 'institution_name', 'major', 'graduation_year'];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
