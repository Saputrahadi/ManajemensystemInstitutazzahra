<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Counter NIM per Prodi + tahun ajaran — pola sama dengan
     * `admission_periods.last_sequence` (nomor pendaftaran) dan `SkCounter`
     * di project sistem-yayasan-laravel: dikunci (`lockForUpdate`) saat NIM
     * diterbitkan supaya aman dari race condition. Dipisah dari
     * `study_programs` (tabel Core) supaya urusan penomoran PMB tidak
     * bocor ke domain Core.
     */
    public function up(): void
    {
        Schema::create('nim_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->string('academic_year');
            $table->unsignedInteger('seq')->default(0);
            $table->timestamps();

            $table->unique(['study_program_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nim_counters');
    }
};
