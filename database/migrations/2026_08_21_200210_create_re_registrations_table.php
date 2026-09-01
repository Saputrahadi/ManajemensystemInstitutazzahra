<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registrasi ulang — langkah terakhir alur PMB sebelum Calon Mahasiswa
     * resmi jadi Mahasiswa. `nim` diisi di sini begitu registrasi ulang
     * selesai (lihat blueprint "Alur Inti Mahasiswa": NIM terbit duluan,
     * status aktif menyusul setelah UKT lunas — dua hal berbeda).
     *
     * Belum ada foreign key ke tabel `students` — domain Mahasiswa (dengan
     * tabel students/student_profiles/dst.) belum dibangun. Begitu domain
     * itu ada, tambahkan `student_id` lewat migrasi baru, jangan ubah
     * migrasi ini.
     */
    public function up(): void
    {
        Schema::create('re_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status')->default('belum'); // belum, proses, selesai
            $table->string('nim')->nullable()->unique();
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_registrations');
    }
};
