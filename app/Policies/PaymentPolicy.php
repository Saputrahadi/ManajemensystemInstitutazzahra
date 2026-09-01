<?php

namespace App\Policies;

use App\Models\Keuangan\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.payment.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasPermission('keuangan.payment.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('keuangan.payment.verify');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermission('keuangan.payment.verify');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermission('keuangan.payment.verify');
    }
}
