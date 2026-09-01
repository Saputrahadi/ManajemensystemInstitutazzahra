<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Kartu Rencana Studi — satu header per mahasiswa per tahun akademik + term. */
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('academic_year'); // mis. 2026/2027
            $table->string('term'); // ganjil, genap
            $table->string('status')->default('draft'); // draft, diajukan, disetujui, ditolak
            $table->unsignedTinyInteger('total_credits')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
