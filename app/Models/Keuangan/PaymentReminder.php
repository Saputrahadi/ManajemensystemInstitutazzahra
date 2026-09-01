<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Log reminder yang sudah dikirim per tagihan (H-7/H-3/H-1/overdue) — dipakai Services\Keuangan\PaymentReminderService supaya tidak kirim dobel. */
class PaymentReminder extends Model
{
    protected $fillable = ['invoice_id', 'reminder_type', 'channel', 'sent_at'];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
