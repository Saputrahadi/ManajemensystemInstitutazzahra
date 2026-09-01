<?php

namespace App\Policies;

use App\Models\Core\Unit;
use App\Models\User;

/**
 * Contoh kanonis data-scoping RBAC dari blueprint: "Kaprodi Prodi A tidak bisa
 * lihat/ubah data Prodi B". viewAny dibatasi lewat User::visibleTo() di
 * Controller/Livewire; view/update di sini adalah pemeriksaan per-baris.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('core.user.view');
    }

    public function view(User $user, User $target): bool
    {
        if (! $user->hasPermission('core.user.view')) {
            return false;
        }

        return $this->sameOrDescendantUnit($user, $target);
    }

    public function update(User $user, User $target): bool
    {
        if (! $user->hasPermission('core.user.update')) {
            return false;
        }

        return $this->sameOrDescendantUnit($user, $target);
    }

    public function delete(User $user, User $target): bool
    {
        if ($target->hasRole('super_admin')) {
            return false; // super_admin tidak bisa dihapus lewat UI
        }

        return $user->hasPermission('core.user.delete') && $this->sameOrDescendantUnit($user, $target);
    }

    private function sameOrDescendantUnit(User $user, User $target): bool
    {
        if ($user->hasInstitutionWideRole()) {
            return true;
        }

        if ($target->unit_id === null) {
            return false;
        }

        foreach ($user->scopedUnitIds() as $unitId) {
            if ($unitId === $target->unit_id) {
                return true;
            }

            $unit = Unit::find($unitId);
            if ($unit && in_array($target->unit_id, $unit->descendants(), true)) {
                return true;
            }
        }

        return false;
    }
}
