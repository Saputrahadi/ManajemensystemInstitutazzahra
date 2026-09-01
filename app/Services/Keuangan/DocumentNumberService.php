<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\PaymentDocumentCounter;

/**
 * Penomoran invoice/kwitansi — row-locked per document_type+tahun, pola
 * sama dengan SkCounter (sistem-yayasan-laravel) dan NimCounter (Pmb).
 * Dipanggil di dalam transaksi si pemanggil (InvoiceService/PaymentVerificationService),
 * bukan membuka transaksi sendiri, supaya nomor & baris terkait commit bersamaan.
 */
class DocumentNumberService
{
    public function next(string $documentType): string
    {
        $year = (string) now()->year;

        $counter = PaymentDocumentCounter::where('document_type', $documentType)
            ->where('year', $year)
            ->lockForUpdate()
            ->first();

        if (! $counter) {
            $counter = PaymentDocumentCounter::create([
                'document_type' => $documentType,
                'year' => $year,
                'seq' => 0,
            ]);
        }

        $counter->increment('seq');

        $prefix = $documentType === 'receipt' ? 'KWT' : 'INV';

        return sprintf('%s/%s/%05d', $prefix, $year, $counter->seq);
    }
}
