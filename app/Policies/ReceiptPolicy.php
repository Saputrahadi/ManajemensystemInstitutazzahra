<?php

namespace App\Policies;

use App\Models\Keuangan\Receipt;
use App\Models\User;

class ReceiptPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.payment.view');
    }

    public function view(User $user, Receipt $receipt): bool
    {
        return $user->hasPermission('keuangan.payment.view');
    }

    public function delete(User $user, Receipt $receipt): bool
    {
        return $user->hasPermission('keuangan.payment.verify');
    }
}
