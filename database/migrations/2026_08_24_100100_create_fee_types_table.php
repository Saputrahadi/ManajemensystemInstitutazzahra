<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Jenis Tagihan (Master Tarif) — katalog jenis biaya, blueprint modul "03. Keuangan & Pembayaran". */
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category'); // ukt, pmb, registrasi, wisuda, layanan, beasiswa, dispensasi
            $table->boolean('is_recurring')->default(false); // true untuk UKT (per semester), false untuk biaya sekali bayar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
