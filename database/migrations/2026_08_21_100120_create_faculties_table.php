<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data Fakultas — lihat blueprint #erd. Companion table untuk baris
     * `organizational_units` bertipe "fakultas" (lihat catatan di migrasi
     * create_institutions_table untuk kenapa polanya begini).
     */
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizational_unit_id')->unique()->constrained('organizational_units')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('dean_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
