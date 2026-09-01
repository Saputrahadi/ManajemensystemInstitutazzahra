<?php

namespace App\Policies;

use App\Models\Keuangan\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('keuangan.invoice.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('keuangan.invoice.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('keuangan.invoice.manage');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('keuangan.invoice.manage');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('keuangan.invoice.manage');
    }
}
