<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Perpanjangan jatuh tempo — "Human Decision, System Execution": pimpinan memutuskan, sistem menjalankan (lihat DueDateExtensionApproval). */
class DueDateExtension extends Model
{
    use Auditable;

    protected $fillable = ['invoice_id', 'requested_by', 'reason', 'requested_due_date', 'status'];

    protected function casts(): array
    {
        return [
            'requested_due_date' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(DueDateExtensionApproval::class)->latest('decided_at');
    }
}
