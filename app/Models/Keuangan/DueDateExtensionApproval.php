<?php

namespace App\Models\Keuangan;

use App\Models\Core\Concerns\Auditable;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Keputusan pimpinan atas satu DueDateExtension — riwayat append-only,
 * pola sama dengan KrsApproval/GradeChangeApproval. Kalau disetujui,
 * langsung menjalankan contoh "Human Decision, System Execution" persis
 * seperti di blueprint: ubah tanggal jatuh tempo Invoice + kirim
 * notifikasi ke mahasiswa — bukan cuma catat status.
 */
class DueDateExtensionApproval extends Model
{
    use Auditable;

    protected $fillable = ['due_date_extension_id', 'approver_id', 'status', 'notes', 'decided_at'];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (DueDateExtensionApproval $approval) {
            $extension = $approval->dueDateExtension;
            $extension->update(['status' => $approval->status]);

            if ($approval->status === 'disetujui') {
                $invoice = $extension->invoice;
                $invoice->update(['due_date' => $extension->requested_due_date]);

                $invoice->student->user->notify(new GenericNotification(
                    title: 'Perpanjangan Jatuh Tempo Disetujui',
                    body: "Jatuh tempo tagihan {$invoice->invoice_number} diperpanjang sampai {$invoice->due_date->translatedFormat('d F Y')}.",
                ));
            }
        });
    }

    public function dueDateExtension(): BelongsTo
    {
        return $this->belongsTo(DueDateExtension::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
