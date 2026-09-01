<?php

namespace App\Policies;

use App\Models\Core\Setting;
use App\Models\User;

/**
 * Pengaturan sistem (Core System). Satu permission `core.setting.manage`
 * menutup seluruh CRUD — sengaja institution-wide, tidak di-scope per-unit.
 */
class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('core.setting.manage');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->hasPermission('core.setting.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('core.setting.manage');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->hasPermission('core.setting.manage');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->hasPermission('core.setting.manage');
    }
}
