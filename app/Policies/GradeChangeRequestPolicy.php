<?php

namespace App\Policies;

use App\Models\Nilai\GradeChangeRequest;
use App\Models\User;

class GradeChangeRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('nilai.finalization.view');
    }

    public function view(User $user, GradeChangeRequest $gradeChangeRequest): bool
    {
        return $user->hasPermission('nilai.finalization.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('nilai.finalization.manage');
    }

    public function update(User $user, GradeChangeRequest $gradeChangeRequest): bool
    {
        return $user->hasPermission('nilai.finalization.manage') || $user->hasPermission('nilai.change.approve');
    }

    public function delete(User $user, GradeChangeRequest $gradeChangeRequest): bool
    {
        return $user->hasPermission('nilai.finalization.manage');
    }
}
