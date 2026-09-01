<?php

namespace App\Policies;

use App\Models\Kurikulum\Curriculum;
use App\Models\User;

class CurriculumPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('kurikulum.curriculum.view');
    }

    public function view(User $user, Curriculum $curriculum): bool
    {
        return $user->hasPermission('kurikulum.curriculum.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('kurikulum.curriculum.manage');
    }

    public function update(User $user, Curriculum $curriculum): bool
    {
        return $user->hasPermission('kurikulum.curriculum.manage');
    }

    public function delete(User $user, Curriculum $curriculum): bool
    {
        return $user->hasPermission('kurikulum.curriculum.manage');
    }
}
