<?php

namespace App\Models\Mahasiswa;

use App\Models\Core\Concerns\Auditable;
use App\Models\Core\StudyProgram;
use App\Models\Dosen\AcademicAdvising;
use App\Models\Dosen\Lecturer;
use App\Models\HasilAkademik\GraduationRecord;
use App\Models\HasilAkademik\GraduationVerification;
use App\Models\HasilAkademik\StudentCumulativeResult;
use App\Models\HasilAkademik\StudentSemesterResult;
use App\Models\HasilAkademik\Transcript;
use App\Models\Keuangan\Invoice;
use App\Models\Krs\Krs;
use App\Models\Kurikulum\Curriculum;
use App\Models\Pmb\Applicant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Master Mahasiswa — lihat blueprint #erd. Applicant → Student dikonversi
 * lewat StudentEnrollmentService, bukan diinput manual (Single Source of Truth).
 */
class Student extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'applicant_id',
        'nim',
        'study_program_id',
        'curriculum_id',
        'advisor_id',
        'admission_year',
        'student_status',
        'entry_path',
        'enrollment_date',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(StudentAddress::class);
    }

    public function families(): HasMany
    {
        return $this->hasMany(StudentFamily::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StudentStatusHistory::class)->orderByDesc('changed_at');
    }

    public function krs(): HasMany
    {
        return $this->hasMany(Krs::class);
    }

    public function semesterResults(): HasMany
    {
        return $this->hasMany(StudentSemesterResult::class);
    }

    public function cumulativeResult(): HasOne
    {
        return $this->hasOne(StudentCumulativeResult::class);
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(Transcript::class);
    }

    public function graduationVerifications(): HasMany
    {
        return $this->hasMany(GraduationVerification::class);
    }

    public function graduationRecord(): HasOne
    {
        return $this->hasOne(GraduationRecord::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'advisor_id');
    }

    public function advisings(): HasMany
    {
        return $this->hasMany(AcademicAdvising::class)->orderByDesc('advising_date');
    }
}
