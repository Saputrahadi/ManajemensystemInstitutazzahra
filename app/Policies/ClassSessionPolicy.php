<?php

namespace App\Policies;

use App\Models\Perkuliahan\ClassSession;
use App\Models\User;

class ClassSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('perkuliahan.session.view');
    }

    public function view(User $user, ClassSession $classSession): bool
    {
        return $user->hasPermission('perkuliahan.session.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('perkuliahan.session.manage');
    }

    public function update(User $user, ClassSession $classSession): bool
    {
        return $user->hasPermission('perkuliahan.session.manage');
    }

    public function delete(User $user, ClassSession $classSession): bool
    {
        return $user->hasPermission('perkuliahan.session.manage');
    }
}
