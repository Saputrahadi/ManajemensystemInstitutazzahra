<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\Invoice;
use App\Notifications\GenericNotification;

/**
 * "jatuh tempo → status overdue" — otomasi yang disebut eksplisit di
 * Prioritas 3 blueprint. Dipanggil dari Artisan command
 * `keuangan:mark-overdue` (lihat app/Console/Commands), belum dijadwalkan
 * lewat cron OS di environment dev ini — jalankan manual/via scheduler
 * produksi nanti.
 */
class InvoiceOverdueService
{
    public function markOverdue(): int
    {
        $invoices = Invoice::where('status', 'belum_bayar')
            ->whereDate('due_date', '<', now()->toDateString())
            ->with('student.user')
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->update(['status' => 'overdue']);

            $invoice->student->user->notify(new GenericNotification(
                title: 'Tagihan Terlambat',
                body: "Tagihan {$invoice->invoice_number} sudah melewati jatuh tempo. Segera lakukan pembayaran.",
            ));
        }

        return $invoices->count();
    }
}
