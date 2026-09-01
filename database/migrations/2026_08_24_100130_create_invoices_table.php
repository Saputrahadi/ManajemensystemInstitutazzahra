<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tagihan Mahasiswa — gabungan langkah "Tagihan Mahasiswa" + "Invoice" di alur Fase 2 (satu dokumen, bukan dua). */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_type_id')->constrained()->restrictOnDelete();
            $table->string('academic_year');
            $table->string('term')->nullable(); // ganjil/genap — null untuk biaya sekali bayar (PMB, wisuda)
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->string('status')->default('belum_bayar'); // belum_bayar, lunas, overdue, dibatalkan
            $table->timestamps();

            $table->unique(['student_id', 'fee_type_id', 'academic_year', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
