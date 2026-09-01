<?php

namespace App\Models\Dosen;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturerExpertise extends Model
{
    use Auditable;

    protected $fillable = ['lecturer_id', 'expertise_name'];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
