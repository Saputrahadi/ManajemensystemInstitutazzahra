<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Kwitansi — diterbitkan otomatis lewat Services\Keuangan\PaymentVerificationService begitu Payment berstatus berhasil. */
class Receipt extends Model
{
    use Auditable;

    protected $fillable = ['payment_id', 'receipt_number', 'issued_at'];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
