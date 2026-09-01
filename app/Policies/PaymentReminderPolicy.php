<?php

namespace App\Policies;

use App\Models\Keuangan\PaymentReminder;
use App\Models\User;

class PaymentReminderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.invoice.view');
    }

    public function view(User $user, PaymentReminder $paymentReminder): bool
    {
        return $user->hasPermission('keuangan.invoice.view');
    }
}
