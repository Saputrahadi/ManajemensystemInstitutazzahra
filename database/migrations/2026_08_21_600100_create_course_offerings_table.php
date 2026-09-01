<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Mata kuliah yang dibuka pada satu tahun akademik + semester (ganjil/genap) tertentu. */
    public function up(): void
    {
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->string('academic_year'); // mis. 2026/2027
            $table->string('term'); // ganjil, genap
            $table->unsignedInteger('quota')->nullable();
            $table->string('status')->default('dibuka'); // dibuka, ditutup
            $table->timestamps();

            $table->unique(['course_id', 'academic_year', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
