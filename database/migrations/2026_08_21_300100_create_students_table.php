<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master Mahasiswa — field persis sesuai blueprint #erd ("Entitas Core
     * dengan field"): "id · user_id · applicant_id · NIM · study_program_id
     * · curriculum_id · admission_year · student_status · entry_path ·
     * enrollment_date". Applicant → Student — dikonversi otomatis lewat
     * StudentEnrollmentService, tidak diinput ulang (prinsip Single Source
     * of Truth).
     *
     * `curriculum_id` sengaja tanpa foreign key — domain Kurikulum
     * (`curricula`) belum dibangun. Tambahkan constraint-nya lewat migrasi
     * baru begitu tabel itu ada, jangan ubah migrasi ini.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('applicant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nim')->unique();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('curriculum_id')->nullable(); // FK menyusul begitu domain Kurikulum ada
            $table->string('admission_year');
            $table->string('student_status')->default('registrasi'); // calon, diterima, registrasi, aktif, cuti, nonaktif, lulus, mengundurkan_diri, dikeluarkan, alumni
            $table->string('entry_path')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
