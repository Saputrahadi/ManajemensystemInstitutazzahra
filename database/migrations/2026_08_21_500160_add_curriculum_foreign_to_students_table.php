<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sesuai catatan di migrasi create_students_table & docs/mahasiswa.md:
     * domain Kurikulum sekarang ada, jadi constraint `curriculum_id`
     * ditambahkan lewat migrasi baru ini — bukan mengubah migrasi lama.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('curriculum_id')->references('id')->on('curricula')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['curriculum_id']);
        });
    }
};
