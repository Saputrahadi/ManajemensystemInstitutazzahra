<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penugasan Dosen ke Prodi — bisa lebih dari satu jenis penugasan
     * sekaligus, mis. blueprint #unit contoh "Dosen Ahmad: Dosen + Dosen
     * Wali di Prodi PAI" jadi dua baris `lecturer_assignments` berbeda
     * `assignment_type` untuk `lecturer_id` yang sama.
     */
    public function up(): void
    {
        Schema::create('lecturer_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->string('assignment_type'); // dosen, dosen_wali
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('aktif'); // aktif, selesai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturer_assignments');
    }
};
