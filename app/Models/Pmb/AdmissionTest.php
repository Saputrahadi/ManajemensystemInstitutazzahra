<?php

namespace App\Models\Pmb;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionTest extends Model
{
    use Auditable;

    protected $fillable = [
        'admission_period_id',
        'name',
        'test_date',
        'location',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'datetime',
        ];
    }

    public function admissionPeriod(): BelongsTo
    {
        return $this->belongsTo(AdmissionPeriod::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(AdmissionTestResult::class);
    }
}
