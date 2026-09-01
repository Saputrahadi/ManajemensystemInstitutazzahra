<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** CPL (Capaian Pembelajaran Lulusan) per Prodi — kerangka BAN-PT, lihat blueprint #struktur (Mutu/LPM). */
    public function up(): void
    {
        Schema::create('learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->constrained()->cascadeOnDelete();
            $table->string('code'); // mis. CPL-01
            $table->text('description');
            $table->timestamps();

            $table->unique(['study_program_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_outcomes');
    }
};
