<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat verifikasi administrasi Calon Mahasiswa — keputusan level
     * pendaftaran (bukan per-dokumen, lihat applicant_documents.status untuk
     * itu). Append-style: satu baris per keputusan verifikasi.
     */
    public function up(): void
    {
        Schema::create('applicant_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verified_by')->constrained('users')->restrictOnDelete();
            $table->string('status'); // disetujui, ditolak, revisi
            $table->text('notes')->nullable();
            $table->timestamp('verified_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_verifications');
    }
};
