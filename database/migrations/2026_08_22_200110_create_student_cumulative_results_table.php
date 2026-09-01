<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** IPK kumulatif — satu baris per mahasiswa, di-recalculate otomatis tiap kali student_semester_results berubah. */
    public function up(): void
    {
        Schema::create('student_cumulative_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('total_credits_earned');
            $table->decimal('cumulative_gpa', 3, 2); // IPK, 0.00 - 4.00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_cumulative_results');
    }
};
