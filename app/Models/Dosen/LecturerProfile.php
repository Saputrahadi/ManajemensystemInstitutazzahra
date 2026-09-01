<?php

namespace App\Models\Dosen;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturerProfile extends Model
{
    use Auditable;

    protected $fillable = ['lecturer_id', 'gender', 'birth_place', 'birth_date', 'phone', 'email', 'photo_path'];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
