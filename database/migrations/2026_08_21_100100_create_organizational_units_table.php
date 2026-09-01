<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Struktur Organisasi (Unit) — lihat blueprint #unit, #struktur, #database.
     * Nama tabel `organizational_units` mengikuti ERD blueprint persis (bagian
     * "Struktur Database" & "Database MVP").
     *
     * Prinsip: "Unit Organisasi != Modul Sistem" — tabel ini murni struktur
     * organisasi (siapa berada di bawah siapa), independen dari daftar modul
     * aplikasi. Tree self-referencing supaya struktur bisa diubah dari data,
     * bukan dari kode, sesuai prinsip "Konfigurasi > Hardcode".
     *
     * Baris institut/fakultas/prodi tetap ada di tabel ini (jadi node dalam
     * tree yang sama dengan Biro/Bagian/Lembaga lain, dan tetap jadi dasar
     * RBAC data-scoping via HasUnitScope) — tapi field khususnya (dekan,
     * akreditasi, jenjang, dst.) disimpan di tabel pendamping `institutions`/
     * `faculties`/`study_programs` (lihat migrasi setelah ini), terhubung
     * lewat `organizational_unit_id`.
     */
    public function up(): void
    {
        Schema::create('organizational_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('organizational_units')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            // Tipe unit bersifat data, bukan enum kaku — 18 unit awal (lihat seeder)
            // dipetakan lewat kolom ini, tapi unit baru bisa ditambah tanpa migrasi baru.
            $table->string('type');
            $table->text('description')->nullable();
            $table->unsignedInteger('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'order_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_units');
    }
};
