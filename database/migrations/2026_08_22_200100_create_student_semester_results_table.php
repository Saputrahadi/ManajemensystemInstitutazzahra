<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Rekap IP (Indeks Prestasi) satu mahasiswa untuk satu tahun akademik + term, dihitung dari grade_finalizations. */
    public function up(): void
    {
        Schema::create('student_semester_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('academic_year');
            $table->string('term'); // ganjil, genap
            $table->unsignedTinyInteger('credits_taken');
            $table->unsignedTinyInteger('credits_earned'); // SKS dengan grade_point > 0
            $table->decimal('semester_gpa', 3, 2); // IP semester, 0.00 - 4.00
            $table->timestamps();

            $table->unique(['student_id', 'academic_year', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_semester_results');
    }
};
