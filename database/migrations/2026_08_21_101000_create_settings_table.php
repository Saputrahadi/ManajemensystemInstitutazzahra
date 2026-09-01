<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pengaturan sistem key-value — prinsip "Konfigurasi > Hardcode".
     * Tarif, periode aktif, dan parameter operasional lain diatur dari sini/modul
     * terkait, bukan ditulis langsung di kode.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable(); // JSON-encoded di level model
            $table->string('group')->default('umum'); // umum, akademik, keuangan, integrasi, dst.
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // boleh dibaca tanpa login (mis. nama institusi)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
