<?php

namespace App\Policies;

use App\Models\Core\Document;
use App\Models\User;

/**
 * Dokumen generik (Core System). Satu permission `core.document.manage`
 * menutup seluruh CRUD, sama seperti granularitas core.unit.manage/
 * core.setting.manage — belum ada kebutuhan scoping per-unit karena
 * `documentable` masih polymorphic generik di Fase 0.
 */
class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('core.document.manage');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->hasPermission('core.document.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('core.document.manage');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->hasPermission('core.document.manage');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermission('core.document.manage');
    }
}
