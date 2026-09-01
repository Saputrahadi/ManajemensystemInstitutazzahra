<?php

namespace App\Policies;

use App\Models\HasilAkademik\GraduationVerification;
use App\Models\User;

class GraduationVerificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function view(User $user, GraduationVerification $graduationVerification): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function update(User $user, GraduationVerification $graduationVerification): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function delete(User $user, GraduationVerification $graduationVerification): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }
}
