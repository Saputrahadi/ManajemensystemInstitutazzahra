<?php

namespace App\Policies;

use App\Models\Kurikulum\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('kurikulum.course.view');
    }

    public function view(User $user, Course $course): bool
    {
        return $user->hasPermission('kurikulum.course.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('kurikulum.course.manage');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasPermission('kurikulum.course.manage');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasPermission('kurikulum.course.manage');
    }
}
