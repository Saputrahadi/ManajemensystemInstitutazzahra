<?php

namespace App\Policies;

use App\Models\Pmb\AdmissionPath;
use App\Models\User;

class AdmissionPathPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function view(User $user, AdmissionPath $admissionPath): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function update(User $user, AdmissionPath $admissionPath): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function delete(User $user, AdmissionPath $admissionPath): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }
}
