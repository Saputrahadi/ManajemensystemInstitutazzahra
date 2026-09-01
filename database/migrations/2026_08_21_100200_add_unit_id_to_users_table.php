<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * unit_id = unit utama/home unit pengguna (mis. Dosen berada di Prodi X).
     * Ini terpisah dari role-scoping di role_user.unit_id — seorang pengguna
     * bisa punya home unit yang berbeda dari unit tempat role tertentu berlaku
     * (mis. Dekan yang juga merangkap dosen di prodi lain).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('organizational_units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_id');
        });
    }
};
