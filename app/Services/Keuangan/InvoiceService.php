<?php

namespace App\Services\Keuangan;

use App\Models\Keuangan\FeeType;
use App\Models\Keuangan\Invoice;
use App\Models\Mahasiswa\Student;
use Illuminate\Support\Facades\DB;

/**
 * Penerbitan tagihan — begitu Invoice dibuat, Virtual Account BSI langsung
 * ikut dibuat dalam transaksi yang sama (meniru "Generate & Inquiry Tagihan"
 * di blueprint modul BSI: begitu tagihan ada, VA-nya harus langsung siap
 * dipakai mahasiswa untuk bayar, bukan langkah terpisah belakangan).
 */
class InvoiceService
{
    public function __construct(private DocumentNumberService $numbers)
    {
    }

    public function createForStudent(Student $student, FeeType $feeType, string $academicYear, ?string $term, float $amount, \DateTimeInterface $dueDate): Invoice
    {
        return DB::transaction(function () use ($student, $feeType, $academicYear, $term, $amount, $dueDate) {
            $invoice = Invoice::create([
                'invoice_number' => $this->numbers->next('invoice'),
                'student_id' => $student->id,
                'fee_type_id' => $feeType->id,
                'academic_year' => $academicYear,
                'term' => $term,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'belum_bayar',
            ]);

            $invoice->virtualAccount()->create([]);

            return $invoice;
        });
    }
}
