<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\Payment;
use App\Models\Keuangan\Receipt;
use App\Notifications\GenericNotification;
use Illuminate\Support\Facades\DB;

/**
 * Satu titik logika "pembayaran terverifikasi" — dipanggil baik dari
 * Services\Keuangan\BsiCallbackService (otomatis, lewat callback BSI)
 * maupun aksi "Verifikasi" manual admin di PaymentResource (fallback
 * non-VA). Sengaja disatukan supaya kedua jalur tidak pernah berbeda
 * perilaku: tandai Payment berhasil, tandai Invoice lunas, terbitkan
 * Kwitansi, dan — kalau tagihannya UKT — jalankan pivot "Alur Inti
 * Mahasiswa" blueprint: status mahasiswa `registrasi` otomatis jadi `aktif`.
 */
class PaymentVerificationService
{
    public function __construct(private DocumentNumberService $numbers)
    {
    }

    public function verify(Payment $payment, ?int $verifiedBy = null): Payment
    {
        return DB::transaction(function () use ($payment, $verifiedBy) {
            $payment->update([
                'status' => 'berhasil',
                'paid_at' => $payment->paid_at ?? now(),
                'verified_by' => $verifiedBy,
                'verified_at' => now(),
            ]);

            $invoice = $payment->invoice;
            $invoice->update(['status' => 'lunas']);

            Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => $this->numbers->next('receipt'),
                'issued_at' => now(),
            ]);

            $student = $invoice->student;

            if ($invoice->feeType->category === 'ukt' && $student->student_status === 'registrasi') {
                $student->update(['student_status' => 'aktif']);
            }

            // getRawOriginal(), bukan accessor bercast decimal — SQLite mengembalikan kolom
            // decimal sebagai float PHP mentah begitu model di-query ulang, yang di-deprecate
            // brick/math (lihat catatan lengkap di docs/hasil-akademik.md).
            $amount = number_format((float) $payment->getRawOriginal('amount_paid'), 0, ',', '.');

            $student->user->notify(new GenericNotification(
                title: 'Pembayaran Diterima',
                body: "Tagihan {$invoice->invoice_number} sebesar Rp{$amount} telah lunas.",
            ));

            return $payment;
        });
    }
}
