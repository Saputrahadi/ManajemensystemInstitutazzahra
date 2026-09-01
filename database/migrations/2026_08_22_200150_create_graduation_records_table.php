<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Catatan kelulusan resmi — satu baris per mahasiswa yang lulus. */
    public function up(): void
    {
        Schema::create('graduation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $table->date('graduation_date');
            $table->decimal('final_gpa', 3, 2);
            $table->string('thesis_title')->nullable();
            $table->string('graduation_number')->nullable()->unique(); // nomor ijazah
            $table->string('status')->default('lulus'); // lulus, dibatalkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_records');
    }
};
