<?php

namespace App\Models\Core;

use App\Models\Core\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Data utama Institut — lihat blueprint #erd. Companion table 1:1 untuk baris
 * `organizational_units` bertipe "institut" (lihat Unit::institution()).
 */
class Institution extends Model
{
    use Auditable;

    protected $fillable = [
        'organizational_unit_id',
        'code',
        'name',
        'short_name',
        'address',
        'phone',
        'email',
        'website',
        'status',
    ];

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'organizational_unit_id');
    }

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }
}
