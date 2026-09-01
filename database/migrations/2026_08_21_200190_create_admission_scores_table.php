<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rekap skor gabungan per Calon Mahasiswa (satu baris per applicant) —
     * dipakai sebagai dasar admission_decisions, bukan skor per komponen
     * (komponen individualnya ada di admission_test_results/interview_results).
     */
    public function up(): void
    {
        Schema::create('admission_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('test_score', 5, 2)->nullable();
            $table->decimal('interview_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_scores');
    }
};
