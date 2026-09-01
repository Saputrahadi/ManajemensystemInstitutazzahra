<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master Dosen — lihat blueprint #erd (domain Dosen) & alur "10. Sistem
     * Dosen": Profil, NIDN/NIDK, Pendidikan, Keahlian, Jabatan, Status.
     * Satu akun login per dosen (pola sama dengan Employee↔User di project
     * sistem-yayasan-laravel) — dibuat sekaligus lewat LecturerResource,
     * bukan dikonversi dari alur lain seperti Mahasiswa dari PMB.
     */
    public function up(): void
    {
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nidn_nidk')->unique();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->string('academic_rank')->nullable(); // jabatan akademik: asisten ahli, lektor, dst.
            $table->string('employment_status')->default('tetap'); // tetap, tidak_tetap
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
