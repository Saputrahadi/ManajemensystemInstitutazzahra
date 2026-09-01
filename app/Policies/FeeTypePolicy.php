<?php

namespace App\Policies;

use App\Models\Keuangan\FeeType;
use App\Models\User;

class FeeTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.fee_type.view');
    }

    public function view(User $user, FeeType $feeType): bool
    {
        return $user->hasPermission('keuangan.fee_type.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('keuangan.fee_type.manage');
    }

    public function update(User $user, FeeType $feeType): bool
    {
        return $user->hasPermission('keuangan.fee_type.manage');
    }

    public function delete(User $user, FeeType $feeType): bool
    {
        return $user->hasPermission('keuangan.fee_type.manage');
    }
}
