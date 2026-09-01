<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\Invoice;
use App\Models\Keuangan\PaymentReminder;
use App\Notifications\GenericNotification;
use Carbon\Carbon;

/**
 * Reminder H-7/H-3/H-1 — otomasi eksplisit di Prioritas 3 blueprint.
 * `payment_reminders` mencegah kirim dobel per invoice+jenis reminder
 * (unique constraint) kalau command ini dijalankan lebih dari sekali
 * dalam satu hari. Dipanggil dari Artisan command `keuangan:send-reminders`.
 */
class PaymentReminderService
{
    private const OFFSETS = ['h7' => 7, 'h3' => 3, 'h1' => 1];

    public function sendDueReminders(): int
    {
        $sent = 0;

        foreach (self::OFFSETS as $type => $daysBefore) {
            $targetDate = Carbon::now()->addDays($daysBefore)->toDateString();

            $invoices = Invoice::where('status', 'belum_bayar')
                ->whereDate('due_date', $targetDate)
                ->whereDoesntHave('reminders', fn ($query) => $query->where('reminder_type', $type))
                ->with('student.user')
                ->get();

            foreach ($invoices as $invoice) {
                $invoice->student->user->notify(new GenericNotification(
                    title: 'Pengingat Jatuh Tempo Tagihan',
                    body: "Tagihan {$invoice->invoice_number} jatuh tempo pada {$invoice->due_date->translatedFormat('d F Y')} ({$daysBefore} hari lagi).",
                ));

                PaymentReminder::create([
                    'invoice_id' => $invoice->id,
                    'reminder_type' => $type,
                    'channel' => 'notifikasi',
                    'sent_at' => now(),
                ]);

                $sent++;
            }
        }

        return $sent;
    }
}
