<?php

namespace App\Policies;

use App\Models\HasilAkademik\GraduationRecord;
use App\Models\User;

class GraduationRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function view(User $user, GraduationRecord $graduationRecord): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function update(User $user, GraduationRecord $graduationRecord): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }

    public function delete(User $user, GraduationRecord $graduationRecord): bool
    {
        return $user->hasPermission('hasil_akademik.graduation.manage');
    }
}
