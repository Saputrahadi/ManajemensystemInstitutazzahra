<?php

namespace App\Policies;

use App\Models\Krs\Krs;
use App\Models\User;

class KrsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('krs.view');
    }

    public function view(User $user, Krs $krs): bool
    {
        return $user->hasPermission('krs.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('krs.manage');
    }

    public function update(User $user, Krs $krs): bool
    {
        return $user->hasPermission('krs.manage') || $user->hasPermission('krs.approve');
    }

    public function delete(User $user, Krs $krs): bool
    {
        return $user->hasPermission('krs.manage');
    }
}
