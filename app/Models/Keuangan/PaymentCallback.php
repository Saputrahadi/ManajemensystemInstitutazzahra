<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Log mentah tiap callback masuk dari BSI — dicatat sebelum diproses, supaya kegagalan pemrosesan tetap punya jejak. */
class PaymentCallback extends Model
{
    use Auditable;

    protected $fillable = ['virtual_account_id', 'payment_id', 'raw_payload', 'processed', 'error_message', 'received_at'];

    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
            'processed' => 'boolean',
            'received_at' => 'datetime',
        ];
    }

    public function virtualAccount(): BelongsTo
    {
        return $this->belongsTo(VirtualAccount::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
