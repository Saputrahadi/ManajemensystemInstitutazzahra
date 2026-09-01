<?php

namespace App\Policies;

use App\Models\Nilai\GradeFinalization;
use App\Models\User;

class GradeFinalizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('nilai.finalization.view');
    }

    public function view(User $user, GradeFinalization $gradeFinalization): bool
    {
        return $user->hasPermission('nilai.finalization.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('nilai.finalization.manage');
    }

    public function update(User $user, GradeFinalization $gradeFinalization): bool
    {
        return $user->hasPermission('nilai.finalization.manage') || $user->hasPermission('nilai.change.approve');
    }

    public function delete(User $user, GradeFinalization $gradeFinalization): bool
    {
        return $user->hasPermission('nilai.finalization.manage');
    }
}
