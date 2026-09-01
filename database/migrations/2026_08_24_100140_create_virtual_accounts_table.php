<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Virtual Account BSI per tagihan — blueprint modul "04. Integrasi Bank BSI". Satu VA per invoice. */
    public function up(): void
    {
        Schema::create('virtual_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('va_number')->unique();
            $table->string('bank_code')->default('BSI');
            $table->string('status')->default('aktif'); // aktif, kadaluarsa, dipakai
            $table->timestamp('generated_at');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_accounts');
    }
};
