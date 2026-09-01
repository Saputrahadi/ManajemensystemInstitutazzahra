<?php

namespace App\Services\Core;

use App\Models\Core\Role;
use App\Models\Core\Unit;
use App\Models\User;

/**
 * Statistik dashboard Core System — dipisah dari Controller sesuai lapisan
 * Domain (Service/Action) di blueprint #arsitektur, supaya logic-nya bisa
 * dipakai ulang (mis. widget Filament) dan dites terpisah dari HTTP layer.
 */
class DashboardStatsService
{
    public function forUser(User $user): array
    {
        $visibleUsers = User::query()->visibleTo($user);

        return [
            'total_users' => (clone $visibleUsers)->count(),
            'active_users' => (clone $visibleUsers)->where('status', 'aktif')->count(),
            'total_units' => $this->visibleUnitCount($user),
            'total_roles' => Role::count(),
        ];
    }

    /** Sama seperti logika HasUnitScope: seluruh unit jika institution-wide, sebaliknya unit yang di-scope + turunannya. */
    private function visibleUnitCount(User $user): int
    {
        if ($user->hasInstitutionWideRole()) {
            return Unit::count();
        }

        return collect($user->scopedUnitIds())
            ->flatMap(fn (int $id) => [$id, ...(Unit::find($id)?->descendants() ?? [])])
            ->unique()
            ->count();
    }
}
