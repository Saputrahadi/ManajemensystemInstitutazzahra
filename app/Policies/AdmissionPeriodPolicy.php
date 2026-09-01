<?php

namespace App\Policies;

use App\Models\Pmb\AdmissionPeriod;
use App\Models\User;

class AdmissionPeriodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function view(User $user, AdmissionPeriod $admissionPeriod): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function update(User $user, AdmissionPeriod $admissionPeriod): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }

    public function delete(User $user, AdmissionPeriod $admissionPeriod): bool
    {
        return $user->hasPermission('pmb.period.manage');
    }
}
