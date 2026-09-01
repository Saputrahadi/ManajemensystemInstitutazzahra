<?php

namespace App\Models\Core\Concerns;

use App\Models\Core\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Trait Auditable — tempel ke model manapun yang perubahannya wajib tercatat
 * (lihat prinsip "Audit Everywhere" di blueprint #arsitektur). Mencatat
 * created/updated/deleted secara otomatis lewat model events, jadi tidak
 * bergantung pada developer ingat memanggilnya manual di tiap Controller/Service.
 *
 * Kolom yang sengaja dikecualikan dari before/after diff didaftarkan lewat
 * properti opsional $auditExcept pada model yang memakai trait ini
 * (mis. ['updated_at'] atau kolom sensitif seperti 'password').
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAudit('created', null, $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $except = property_exists($model, 'auditExcept') ? $model->auditExcept : [];
            $changes = array_diff_key($changes, array_flip($except));

            if (empty($changes)) {
                return; // tidak ada perubahan substantif (mis. hanya updated_at)
            }

            $before = [];
            foreach (array_keys($changes) as $key) {
                $before[$key] = $model->getOriginal($key);
            }

            $model->recordAudit('updated', $before, $changes);
        });

        static::deleted(function ($model) {
            $model->recordAudit('deleted', $model->getAuditableAttributes(), null);
        });
    }

    protected function getAuditableAttributes(): array
    {
        $except = property_exists($this, 'auditExcept') ? $this->auditExcept : [];

        return array_diff_key($this->getAttributes(), array_flip($except));
    }

    protected function recordAudit(string $action, ?array $before, ?array $after): void
    {
        $user = Auth::user();

        AuditLog::create([
            'user_id' => $user?->id,
            'role_snapshot' => $user?->roles()->pluck('code')->join(', '),
            'action' => $action,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'description' => sprintf('%s %s #%s', class_basename($this), $action, $this->getKey()),
            'before' => $before,
            'after' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
