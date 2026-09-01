<?php

namespace App\Policies;

use App\Models\Pmb\AdmissionTest;
use App\Models\User;

class AdmissionTestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }

    public function view(User $user, AdmissionTest $admissionTest): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }

    public function update(User $user, AdmissionTest $admissionTest): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }

    public function delete(User $user, AdmissionTest $admissionTest): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }
}
