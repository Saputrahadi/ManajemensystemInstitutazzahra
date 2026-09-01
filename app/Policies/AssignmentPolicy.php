<?php

namespace App\Policies;

use App\Models\Perkuliahan\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('perkuliahan.assignment.view');
    }

    public function view(User $user, Assignment $assignment): bool
    {
        return $user->hasPermission('perkuliahan.assignment.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('perkuliahan.assignment.manage');
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->hasPermission('perkuliahan.assignment.manage');
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->hasPermission('perkuliahan.assignment.manage');
    }
}
