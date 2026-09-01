<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Hasil ujian seleksi per Calon Mahasiswa. */
    public function up(): void
    {
        Schema::create('admission_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_test_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('status')->nullable(); // lulus, tidak_lulus
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['applicant_id', 'admission_test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_test_results');
    }
};
