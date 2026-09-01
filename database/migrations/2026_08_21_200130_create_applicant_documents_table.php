<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dokumen yang diupload Calon Mahasiswa (ijazah, KTP, KK, foto, dst.) —
     * tabel domain PMB tersendiri, terpisah dari `documents` generik di Core
     * System, karena di sini butuh field workflow verifikasi per-dokumen
     * (status, verified_by, verified_at) yang tidak relevan untuk dokumen
     * Core (SK, surat, dst.).
     */
    public function up(): void
    {
        Schema::create('applicant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('document_type'); // mis. ijazah, ktp, kk, foto — bebas data-driven
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('status')->default('menunggu'); // menunggu, terverifikasi, ditolak
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_documents');
    }
};
