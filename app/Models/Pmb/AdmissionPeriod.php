<?php

namespace App\Models\Pmb;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionPeriod extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'academic_year',
        'opens_at',
        'closes_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'date',
            'closes_at' => 'date',
        ];
    }

    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }

    public function admissionTests(): HasMany
    {
        return $this->hasMany(AdmissionTest::class);
    }
}
