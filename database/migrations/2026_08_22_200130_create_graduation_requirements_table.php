<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Syarat kelulusan per Program Studi — satu set syarat aktif per prodi. */
    public function up(): void
    {
        Schema::create('graduation_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('min_credits');
            $table->decimal('min_gpa', 3, 2);
            $table->unsignedTinyInteger('max_study_years')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_requirements');
    }
};
