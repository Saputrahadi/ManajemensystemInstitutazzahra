<?php

namespace App\Models\Krs;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Kartu Rencana Studi — header per mahasiswa per tahun akademik + term. */
class Krs extends Model
{
    use Auditable;

    protected $table = 'krs';

    protected $fillable = ['student_id', 'academic_year', 'term', 'status', 'total_credits', 'submitted_at'];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(KrsDetail::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(KrsApproval::class)->latest('decided_at');
    }

    public function recalculateTotalCredits(): void
    {
        $this->updateQuietly(['total_credits' => $this->details()->sum('credits')]);
    }
}
