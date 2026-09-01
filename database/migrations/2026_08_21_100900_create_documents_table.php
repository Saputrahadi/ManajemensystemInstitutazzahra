<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * File/Document generik (Core System) — dipakai modul lain lewat relasi
     * polymorphic (documentable), mis. Dokumen Mutu, SK Pegawai, Berkas PMB.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('documentable');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category')->nullable(); // mis. "SK", "Berkas PMB", "Dokumen Mutu"
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->boolean('is_public')->default(false);
            $table->timestamp('expires_at')->nullable(); // dipakai cron pengecekan dokumen kadaluarsa
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
