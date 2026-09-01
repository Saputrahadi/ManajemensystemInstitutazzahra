<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Virtual Account BSI per Invoice. Nomor VA dibentuk deterministik dari
 * kode perusahaan (`config('bsi.company_code')`) + ID invoice — bukan
 * hasil panggilan API BSI Generate VA yang sesungguhnya, karena kredensial
 * sandbox belum tersedia (lihat docs/keuangan.md). Format & panjangnya
 * tetap mengikuti konvensi VA bank pada umumnya, jadi tinggal diganti
 * pemanggilan API nyata begitu kredensial ada — struktur data & alur
 * callback-nya sudah benar sekarang.
 */
class VirtualAccount extends Model
{
    use Auditable;

    protected $fillable = ['invoice_id', 'va_number', 'bank_code', 'status', 'generated_at', 'expired_at'];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VirtualAccount $va) {
            $va->va_number ??= config('bsi.company_code').str_pad((string) $va->invoice_id, 11, '0', STR_PAD_LEFT);
            $va->generated_at ??= now();
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function callbacks(): HasMany
    {
        return $this->hasMany(PaymentCallback::class);
    }
}
