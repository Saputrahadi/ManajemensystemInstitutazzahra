<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit Trail — cross-cutting, wajib untuk semua modul (lihat blueprint #audit).
     * Tabel ini append-only: tidak ada updated_at, dan model AuditLog tidak boleh
     * di-update/delete lewat aplikasi (lihat Policy AuditLogPolicy).
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Snapshot nama role saat aksi terjadi — supaya riwayat tetap akurat
            // walau role pengguna berubah/dihapus di kemudian hari.
            $table->string('role_snapshot')->nullable();
            $table->string('action'); // created, updated, deleted, login, approved, dst.
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->text('description')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
