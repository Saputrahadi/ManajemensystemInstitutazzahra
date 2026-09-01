<?php

namespace App\Policies;

use App\Models\Keuangan\FeeTariff;
use App\Models\User;

class FeeTariffPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.tariff.view');
    }

    public function view(User $user, FeeTariff $feeTariff): bool
    {
        return $user->hasPermission('keuangan.tariff.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('keuangan.tariff.manage');
    }

    public function update(User $user, FeeTariff $feeTariff): bool
    {
        return $user->hasPermission('keuangan.tariff.manage');
    }

    public function delete(User $user, FeeTariff $feeTariff): bool
    {
        return $user->hasPermission('keuangan.tariff.manage');
    }
}
