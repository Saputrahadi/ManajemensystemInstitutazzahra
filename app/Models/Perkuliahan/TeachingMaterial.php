<?php

namespace App\Models\Perkuliahan;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingMaterial extends Model
{
    use Auditable;

    protected $fillable = ['class_session_id', 'title', 'description', 'file_path'];

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }
}
