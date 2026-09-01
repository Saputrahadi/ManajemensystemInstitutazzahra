<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Log reminder yang sudah dikirim per tagihan — "reminder H-7/H-3/H-1" di Prioritas 3 blueprint, dipakai supaya tidak kirim reminder dobel. */
    public function up(): void
    {
        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('reminder_type'); // h7, h3, h1, overdue
            $table->string('channel')->default('notifikasi'); // notifikasi, email, whatsapp
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['invoice_id', 'reminder_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reminders');
    }
};
