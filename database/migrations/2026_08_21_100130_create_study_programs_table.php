<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Program Studi — lihat blueprint #erd. Companion table untuk baris
     * `organizational_units` bertipe "prodi" (lihat catatan di migrasi
     * create_institutions_table). Prodi awal: HKI, MBS, PAI, PBA — prodi
     * baru bisa ditambah tanpa ubah struktur fundamental.
     */
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizational_unit_id')->unique()->constrained('organizational_units')->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('faculties')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('degree_level')->nullable();
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accreditation')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
