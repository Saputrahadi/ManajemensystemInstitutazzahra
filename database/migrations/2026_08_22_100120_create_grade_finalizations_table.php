<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Nilai akhir per mahasiswa per Kelas — hasil kalkulasi weighted average dari grade_components + grades. */
    public function up(): void
    {
        Schema::create('grade_finalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('final_score', 5, 2);
            $table->string('letter_grade'); // A, AB, B, BC, C, CD, D, E
            $table->decimal('grade_point', 3, 2); // 0.00 - 4.00
            $table->foreignId('finalized_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('finalized_at');
            $table->timestamps();

            $table->unique(['class_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_finalizations');
    }
};
