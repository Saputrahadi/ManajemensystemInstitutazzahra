<?php

namespace App\Console\Commands;

use App\Services\Keuangan\PaymentReminderService;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'keuangan:send-reminders';

    protected $description = 'Kirim reminder H-7/H-3/H-1 untuk tagihan yang mendekati jatuh tempo (blueprint Prioritas 3)';

    public function handle(PaymentReminderService $service): int
    {
        $count = $service->sendDueReminders();

        $this->info("{$count} reminder dikirim.");

        return self::SUCCESS;
    }
}
