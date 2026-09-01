<?php

namespace App\Policies;

use App\Models\Kelas\CourseClass;
use App\Models\User;

class CourseClassPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('kelas.class.view');
    }

    public function view(User $user, CourseClass $courseClass): bool
    {
        return $user->hasPermission('kelas.class.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('kelas.class.manage');
    }

    public function update(User $user, CourseClass $courseClass): bool
    {
        return $user->hasPermission('kelas.class.manage');
    }

    public function delete(User $user, CourseClass $courseClass): bool
    {
        return $user->hasPermission('kelas.class.manage');
    }
}
