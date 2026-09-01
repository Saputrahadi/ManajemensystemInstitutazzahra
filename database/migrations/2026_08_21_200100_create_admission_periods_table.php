<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Periode/gelombang PMB — lihat blueprint #erd (domain PMB, tabel MVP
     * Tahap 1). Satu Applicant selalu terikat ke satu periode.
     */
    public function up(): void
    {
        Schema::create('admission_periods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('academic_year');
            $table->date('opens_at');
            $table->date('closes_at');
            $table->string('status')->default('draft'); // draft, aktif, ditutup
            $table->unsignedInteger('last_sequence')->default(0); // dikunci (lockForUpdate) saat generate registration_number
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_periods');
    }
};
