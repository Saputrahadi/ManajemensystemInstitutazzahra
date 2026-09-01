<?php

namespace App\Policies;

use App\Models\Nilai\GradeComponent;
use App\Models\User;

class GradeComponentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('nilai.component.view');
    }

    public function view(User $user, GradeComponent $gradeComponent): bool
    {
        return $user->hasPermission('nilai.component.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('nilai.component.manage');
    }

    public function update(User $user, GradeComponent $gradeComponent): bool
    {
        return $user->hasPermission('nilai.component.manage');
    }

    public function delete(User $user, GradeComponent $gradeComponent): bool
    {
        return $user->hasPermission('nilai.component.manage');
    }
}
