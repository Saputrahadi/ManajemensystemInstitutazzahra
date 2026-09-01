<?php

namespace App\Policies;

use App\Models\Pmb\Applicant;
use App\Models\User;

class ApplicantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('pmb.applicant.view');
    }

    public function view(User $user, Applicant $applicant): bool
    {
        return $user->hasPermission('pmb.applicant.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }

    public function update(User $user, Applicant $applicant): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }

    public function delete(User $user, Applicant $applicant): bool
    {
        return $user->hasPermission('pmb.applicant.manage');
    }
}
