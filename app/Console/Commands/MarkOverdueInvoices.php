<?php

namespace App\Console\Commands;

use App\Services\Keuangan\InvoiceOverdueService;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature = 'keuangan:mark-overdue';

    protected $description = 'Tandai tagihan yang sudah lewat jatuh tempo sebagai overdue (blueprint Prioritas 3: "jatuh tempo → status overdue")';

    public function handle(InvoiceOverdueService $service): int
    {
        $count = $service->markOverdue();

        $this->info("{$count} tagihan ditandai overdue.");

        return self::SUCCESS;
    }
}
