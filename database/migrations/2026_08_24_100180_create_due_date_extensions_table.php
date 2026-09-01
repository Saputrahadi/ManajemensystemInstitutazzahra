<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Perpanjangan jatuh tempo — "perpanjangan → approval pimpinan → deadline baru" di Prioritas 3 blueprint. */
    public function up(): void
    {
        Schema::create('due_date_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->text('reason');
            $table->date('requested_due_date');
            $table->string('status')->default('menunggu'); // menunggu, disetujui, ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('due_date_extensions');
    }
};
