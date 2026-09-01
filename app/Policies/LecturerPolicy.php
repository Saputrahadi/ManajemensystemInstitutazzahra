<?php

namespace App\Policies;

use App\Models\Dosen\Lecturer;
use App\Models\User;

class LecturerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('dosen.lecturer.view');
    }

    public function view(User $user, Lecturer $lecturer): bool
    {
        return $user->hasPermission('dosen.lecturer.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('dosen.lecturer.manage');
    }

    public function update(User $user, Lecturer $lecturer): bool
    {
        return $user->hasPermission('dosen.lecturer.manage');
    }

    public function delete(User $user, Lecturer $lecturer): bool
    {
        return $user->hasPermission('dosen.lecturer.manage');
    }
}
