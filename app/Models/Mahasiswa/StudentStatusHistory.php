<?php

namespace App\Models\Mahasiswa;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Append-only — tidak pakai Auditable (riwayat status itu sendiri sudah jadi audit trail). */
class StudentStatusHistory extends Model
{
    protected $fillable = ['student_id', 'status', 'changed_by', 'notes', 'changed_at'];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
