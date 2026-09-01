<?php

namespace App\Policies;

use App\Models\HasilAkademik\StudentCumulativeResult;
use App\Models\User;

class StudentCumulativeResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.result.view');
    }

    public function view(User $user, StudentCumulativeResult $studentCumulativeResult): bool
    {
        return $user->hasPermission('hasil_akademik.result.view');
    }

    public function update(User $user, StudentCumulativeResult $studentCumulativeResult): bool
    {
        return $user->hasPermission('hasil_akademik.result.manage');
    }

    public function delete(User $user, StudentCumulativeResult $studentCumulativeResult): bool
    {
        return $user->hasPermission('hasil_akademik.result.manage');
    }
}
