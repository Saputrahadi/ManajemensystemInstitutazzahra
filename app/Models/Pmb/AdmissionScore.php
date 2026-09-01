<?php

namespace App\Models\Pmb;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionScore extends Model
{
    use Auditable;

    protected $fillable = [
        'applicant_id',
        'test_score',
        'interview_score',
        'final_score',
        'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'computed_at' => 'datetime',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
