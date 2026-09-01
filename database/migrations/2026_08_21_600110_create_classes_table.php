<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Kelas/rombel dari satu course_offering — satu mata kuliah dibuka bisa punya beberapa kelas paralel (A, B, ...). */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
            $table->string('code'); // A, B, C, ...
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();

            $table->unique(['course_offering_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
