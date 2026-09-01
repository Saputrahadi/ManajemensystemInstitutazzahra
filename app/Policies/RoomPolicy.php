<?php

namespace App\Policies;

use App\Models\Kelas\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('kelas.room.view');
    }

    public function view(User $user, Room $room): bool
    {
        return $user->hasPermission('kelas.room.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('kelas.room.manage');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasPermission('kelas.room.manage');
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->hasPermission('kelas.room.manage');
    }
}
