<?php

namespace App\Policies;

use App\Models\Keuangan\DueDateExtension;
use App\Models\User;

class DueDateExtensionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.extension.view');
    }

    public function view(User $user, DueDateExtension $dueDateExtension): bool
    {
        return $user->hasPermission('keuangan.extension.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('keuangan.extension.manage');
    }

    public function update(User $user, DueDateExtension $dueDateExtension): bool
    {
        return $user->hasPermission('keuangan.extension.manage') || $user->hasPermission('keuangan.extension.approve');
    }

    public function delete(User $user, DueDateExtension $dueDateExtension): bool
    {
        return $user->hasPermission('keuangan.extension.manage');
    }
}
