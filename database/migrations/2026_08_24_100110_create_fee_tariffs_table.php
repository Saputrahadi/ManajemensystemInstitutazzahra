<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Nominal tarif per jenis tagihan — bisa berbeda per Prodi (mis. UKT PAI vs UKT Hukum Keluarga), study_program_id null = berlaku untuk semua Prodi. */
    public function up(): void
    {
        Schema::create('fee_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_program_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('academic_year'); // mis. 2026/2027
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->unique(['fee_type_id', 'study_program_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_tariffs');
    }
};
