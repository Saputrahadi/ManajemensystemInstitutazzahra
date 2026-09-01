<?php

namespace App\Models\Pmb;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Calon Mahasiswa — entitas inti domain PMB. Lihat blueprint "Alur Inti
 * Mahasiswa" untuk urutan status penuh (draft → ... → registrasi_ulang).
 */
class Applicant extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'registration_number',
        'admission_period_id',
        'admission_path_id',
        'study_program_id',
        'user_id',
        'name',
        'nik',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'email',
        'parent_name',
        'parent_phone',
        'status',
        'registration_fee_status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function admissionPeriod(): BelongsTo
    {
        return $this->belongsTo(AdmissionPeriod::class);
    }

    public function admissionPath(): BelongsTo
    {
        return $this->belongsTo(AdmissionPath::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ApplicantVerification::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(AdmissionTestResult::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(AdmissionScore::class);
    }

    public function decision(): HasOne
    {
        return $this->hasOne(AdmissionDecision::class);
    }

    public function reRegistration(): HasOne
    {
        return $this->hasOne(ReRegistration::class);
    }
}
