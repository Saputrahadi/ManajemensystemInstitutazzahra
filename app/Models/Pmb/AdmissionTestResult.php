<?php

namespace App\Models\Pmb;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionTestResult extends Model
{
    use Auditable;

    protected $fillable = [
        'applicant_id',
        'admission_test_id',
        'score',
        'status',
        'notes',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function admissionTest(): BelongsTo
    {
        return $this->belongsTo(AdmissionTest::class);
    }
}
