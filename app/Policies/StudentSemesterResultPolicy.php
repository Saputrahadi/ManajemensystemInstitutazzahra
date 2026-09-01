<?php

namespace App\Policies;

use App\Models\HasilAkademik\StudentSemesterResult;
use App\Models\User;

class StudentSemesterResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.result.view');
    }

    public function view(User $user, StudentSemesterResult $studentSemesterResult): bool
    {
        return $user->hasPermission('hasil_akademik.result.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('hasil_akademik.result.manage');
    }

    public function update(User $user, StudentSemesterResult $studentSemesterResult): bool
    {
        return $user->hasPermission('hasil_akademik.result.manage');
    }

    public function delete(User $user, StudentSemesterResult $studentSemesterResult): bool
    {
        return $user->hasPermission('hasil_akademik.result.manage');
    }
}
