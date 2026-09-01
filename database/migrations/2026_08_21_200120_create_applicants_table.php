<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Calon Mahasiswa — entitas inti domain PMB, lihat blueprint #erd &
     * "Alur Inti Mahasiswa". `registration_number` diterbitkan begitu
     * pendaftaran dimulai (mekanisme generate-nya menyusul saat Controller/
     * Service PMB dibangun — di sini baru kolomnya).
     *
     * `status` mengikuti "Status Workflow Umum" di blueprint #alurbisnis:
     * draft → mendaftar → menunggu_verifikasi → terverifikasi →
     * mengikuti_seleksi → lulus/tidak_lulus → registrasi_ulang → selesai.
     *
     * `user_id` nullable — baru terisi begitu Calon Mahasiswa mendapat akun
     * portal ("Akun Calon Mahasiswa" di alur inti mahasiswa).
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->foreignId('admission_period_id')->constrained()->restrictOnDelete();
            $table->foreignId('admission_path_id')->constrained()->restrictOnDelete();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('nik')->nullable();
            $table->string('gender')->nullable(); // L / P
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();

            $table->string('status')->default('draft');
            $table->string('registration_fee_status')->default('belum_bayar'); // belum_bayar, lunas — detail tagihan menyusul domain Keuangan

            $table->timestamps();
            $table->softDeletes();

            $table->index(['admission_period_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
