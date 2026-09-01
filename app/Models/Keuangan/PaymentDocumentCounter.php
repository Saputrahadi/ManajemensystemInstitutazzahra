<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

/** Counter penomoran dokumen keuangan — dikunci baris (lockForUpdate) saat dipakai, pola sama dengan SkCounter/NimCounter. */
class PaymentDocumentCounter extends Model
{
    protected $fillable = ['document_type', 'year', 'seq'];
}
