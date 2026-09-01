<?php

namespace App\Policies;

use App\Models\Ujian\Exam;
use App\Models\User;

class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ujian.exam.view');
    }

    public function view(User $user, Exam $exam): bool
    {
        return $user->hasPermission('ujian.exam.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('ujian.exam.manage');
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->hasPermission('ujian.exam.manage');
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $user->hasPermission('ujian.exam.manage');
    }
}
