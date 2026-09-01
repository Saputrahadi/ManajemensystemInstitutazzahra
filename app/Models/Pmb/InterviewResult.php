<?php

namespace App\Models\Pmb;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewResult extends Model
{
    use Auditable;

    protected $fillable = [
        'interview_id',
        'score',
        'recommendation',
        'notes',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }
}
