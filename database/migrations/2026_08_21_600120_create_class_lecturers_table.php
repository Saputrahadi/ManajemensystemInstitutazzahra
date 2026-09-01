<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Dosen pengampu satu kelas — bisa lebih dari satu dosen per kelas (pengampu utama + asisten). */
    public function up(): void
    {
        Schema::create('class_lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->string('role')->default('pengampu'); // pengampu, asisten
            $table->timestamps();

            $table->unique(['class_id', 'lecturer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_lecturers');
    }
};
