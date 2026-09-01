<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Log keputusan Dosen Wali/admin atas satu KRS — riwayat, bukan status tunggal (KRS bisa ditolak lalu diajukan ulang). */
    public function up(): void
    {
        Schema::create('krs_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained('krs')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->restrictOnDelete();
            $table->string('status'); // disetujui, ditolak
            $table->text('notes')->nullable();
            $table->timestamp('decided_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs_approvals');
    }
};
