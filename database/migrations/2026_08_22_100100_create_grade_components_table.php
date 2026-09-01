<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Komponen penilaian per Kelas (mis. Tugas 30%, UTS 30%, UAS 40%) — `weight` dalam persen. */
    public function up(): void
    {
        Schema::create('grade_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('name'); // Tugas, UTS, UAS, Kuis, ...
            $table->unsignedTinyInteger('weight'); // persen, 0-100

            $table->timestamps();

            $table->unique(['class_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_components');
    }
};
