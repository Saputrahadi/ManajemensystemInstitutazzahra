<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Data keluarga/wali Mahasiswa (1:banyak — ayah, ibu, wali, dst.). */
    public function up(): void
    {
        Schema::create('student_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('relation_type'); // ayah, ibu, wali
            $table->string('name');
            $table->string('occupation')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_families');
    }
};
