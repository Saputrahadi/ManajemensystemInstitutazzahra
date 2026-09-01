<?php

namespace App\Policies;

use App\Models\Keuangan\PaymentCallback;
use App\Models\User;

class PaymentCallbackPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.payment.view');
    }

    public function view(User $user, PaymentCallback $paymentCallback): bool
    {
        return $user->hasPermission('keuangan.payment.view');
    }
}
