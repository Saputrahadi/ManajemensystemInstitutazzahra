<?php

namespace App\Policies;

use App\Models\Core\AuditLog;
use App\Models\User;

/**
 * Audit Log bersifat append-only (lihat blueprint #audit). Siapapun — termasuk
 * Super Admin — tidak boleh mengubah atau menghapus baris lewat aplikasi.
 * Hanya viewAny/view yang bisa true, dan hanya untuk role Auditor/SPI/Super Admin.
 */
class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('core.audit_log.view');
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasPermission('core.audit_log.view');
    }

    public function create(User $user): bool
    {
        return false; // hanya dibuat lewat sistem (trait Auditable), tidak lewat form
    }

    public function update(User $user, AuditLog $auditLog): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $auditLog): bool
    {
        return false;
    }
}
