<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Jenis Tagihan — katalog jenis biaya (UKT, PMB, registrasi, wisuda, layanan, beasiswa, dispensasi). */
class FeeType extends Model
{
    use Auditable;

    protected $fillable = ['code', 'name', 'category', 'is_recurring'];

    protected function casts(): array
    {
        return [
            'is_recurring' => 'boolean',
        ];
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(FeeTariff::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
