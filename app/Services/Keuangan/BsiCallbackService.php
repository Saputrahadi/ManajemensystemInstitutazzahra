<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\Payment;
use App\Models\Keuangan\PaymentCallback;
use App\Models\Keuangan\VirtualAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Titik masuk callback BSI (blueprint modul "04. Integrasi Bank BSI":
 * Callback, Payment Status, Transaction Log, Error Handling). Payload
 * mentah SELALU dicatat lebih dulu ke payment_callbacks sebelum diproses
 * apa pun — supaya kegagalan (VA tidak ditemukan, format tak valid) tetap
 * punya jejak, bukan cuma exception yang hilang di log server.
 *
 * Idempoten: callback dobel untuk VA yang tagihannya sudah lunas tidak
 * membuat Payment kedua — bank pembayaran nyata sering mengirim callback
 * lebih dari sekali untuk transaksi yang sama.
 */
class BsiCallbackService
{
    public function __construct(private PaymentVerificationService $verification)
    {
    }

    public function handle(array $payload): PaymentCallback
    {
        return DB::transaction(function () use ($payload) {
            $callback = PaymentCallback::create([
                'raw_payload' => $payload,
                'processed' => false,
                'received_at' => now(),
            ]);

            $vaNumber = $payload['virtual_account_number'] ?? null;
            $va = $vaNumber ? VirtualAccount::where('va_number', $vaNumber)->first() : null;

            if (! $va) {
                $callback->update(['error_message' => "Virtual Account tidak ditemukan: {$vaNumber}"]);

                return $callback;
            }

            $callback->update(['virtual_account_id' => $va->id]);

            $invoice = $va->invoice;

            if ($invoice->status === 'lunas') {
                $callback->update([
                    'processed' => true,
                    'error_message' => 'Diabaikan — invoice sudah lunas (callback duplikat).',
                ]);

                return $callback;
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'virtual_account_id' => $va->id,
                // getRawOriginal() untuk fallback — lihat catatan di PaymentVerificationService.
                'amount_paid' => $payload['amount'] ?? $invoice->getRawOriginal('amount'),
                'payment_method' => 'va_bsi',
                'status' => 'menunggu',
                'paid_at' => isset($payload['paid_at']) ? Carbon::parse($payload['paid_at']) : now(),
            ]);

            $this->verification->verify($payment);

            $callback->update(['payment_id' => $payment->id, 'processed' => true]);

            return $callback;
        });
    }
}
