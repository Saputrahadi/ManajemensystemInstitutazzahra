<?php

namespace App\Models\Core;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Audit Trail — append-only. Baris di sini tidak boleh diubah/dihapus lewat
 * aplikasi (lihat AuditLogPolicy: update/delete selalu false).
 */
class AuditLog extends Model
{
    public $timestamps = false; // hanya created_at, di-set manual/default DB

    protected $fillable = [
        'user_id',
        'role_snapshot',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'before',
        'after',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'before' => 'array',
            'after' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
