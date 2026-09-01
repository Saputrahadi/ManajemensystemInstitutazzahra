<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dosen Wali — blueprint Prioritas 4 ("Sistem Dosen": ... Dosen Wali,
     * Bimbingan) dan alur KRS ("Persetujuan Dosen PA"). Satu mahasiswa
     * punya satu Dosen Wali aktif pada satu waktu; riwayat penggantian
     * tidak dilacak terpisah (beda dengan StudentStatusHistory) karena
     * blueprint tidak memintanya — cukup field langsung di students,
     * pola sama dengan curriculum_id.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('advisor_id')->nullable()->after('curriculum_id')
                ->constrained('lecturers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('advisor_id');
        });
    }
};
