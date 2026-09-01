<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use App\Models\Mahasiswa\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Tagihan Mahasiswa — nomor invoice diterbitkan otomatis lewat Services\Keuangan\InvoiceNumberGenerator saat baris dibuat. */
class Invoice extends Model
{
    use Auditable;

    protected $fillable = ['invoice_number', 'student_id', 'fee_type_id', 'academic_year', 'term', 'amount', 'due_date', 'status'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    public function virtualAccount(): HasOne
    {
        return $this->hasOne(VirtualAccount::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(DueDateExtension::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(PaymentReminder::class);
    }
}
