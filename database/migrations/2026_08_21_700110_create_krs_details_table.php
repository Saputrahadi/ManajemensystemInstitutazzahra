<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Baris kelas yang diambil dalam satu KRS — `credits` snapshot SKS mata kuliah saat diambil. */
    public function up(): void
    {
        Schema::create('krs_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')->constrained('krs')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->unsignedTinyInteger('credits');
            $table->timestamps();

            $table->unique(['krs_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs_details');
    }
};
