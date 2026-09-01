<?php

namespace App\Policies;

use App\Models\HasilAkademik\GraduationRequirement;
use App\Models\User;

class GraduationRequirementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function view(User $user, GraduationRequirement $graduationRequirement): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function update(User $user, GraduationRequirement $graduationRequirement): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function delete(User $user, GraduationRequirement $graduationRequirement): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }
}
