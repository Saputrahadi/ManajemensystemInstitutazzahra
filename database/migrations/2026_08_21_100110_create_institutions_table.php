<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data utama Institut — lihat blueprint #erd ("Entitas Core dengan field").
     * Tabel pendamping (companion) untuk baris `organizational_units` bertipe
     * "institut": RBAC/data-scoping tetap jalan lewat organizational_units
     * (lihat HasUnitScope), tabel ini murni menyimpan field khusus institusi
     * yang tidak relevan untuk unit organisasi lain (Biro, Lembaga, dst.).
     */
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizational_unit_id')->unique()->constrained('organizational_units')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
