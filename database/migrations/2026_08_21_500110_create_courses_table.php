<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Master Mata Kuliah — dipetakan ke kurikulum lewat curriculum_courses (bukan 1:1 ke satu kurikulum). */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedTinyInteger('credits'); // SKS
            $table->text('description')->nullable();
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
