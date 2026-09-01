<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sesuai catatan di migrasi create_re_registrations_table dan
     * docs/pmb.md: domain Mahasiswa sekarang ada, jadi `student_id`
     * ditambahkan lewat migrasi baru ini — bukan mengubah migrasi lama.
     */
    public function up(): void
    {
        Schema::table('re_registrations', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('applicant_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('re_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
        });
    }
};
