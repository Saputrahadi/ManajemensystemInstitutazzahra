<?php

namespace App\Models\Krs;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Riwayat keputusan Dosen Wali/admin atas satu KRS — KRS bisa ditolak lalu diajukan ulang, jadi ini log, bukan status tunggal. */
class KrsApproval extends Model
{
    use Auditable;

    protected $fillable = ['krs_id', 'approver_id', 'status', 'notes', 'decided_at'];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (KrsApproval $approval) {
            $approval->krs->update(['status' => $approval->status]);
        });
    }

    public function krs(): BelongsTo
    {
        return $this->belongsTo(Krs::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
