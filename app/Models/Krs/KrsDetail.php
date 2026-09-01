<?php

namespace App\Models\Krs;

use App\Models\Core\Concerns\Auditable;
use App\Models\Kelas\CourseClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Satu kelas yang diambil dalam satu KRS. `credits` di-snapshot dari Course saat baris ini dibuat. */
class KrsDetail extends Model
{
    use Auditable;

    protected $fillable = ['krs_id', 'class_id', 'credits'];

    protected static function booted(): void
    {
        static::creating(function (KrsDetail $detail) {
            if (empty($detail->credits)) {
                $detail->credits = $detail->courseClass?->courseOffering?->course?->credits ?? 0;
            }
        });

        static::saved(fn (KrsDetail $detail) => $detail->krs->recalculateTotalCredits());
        static::deleted(fn (KrsDetail $detail) => $detail->krs->recalculateTotalCredits());
    }

    public function krs(): BelongsTo
    {
        return $this->belongsTo(Krs::class);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }
}
