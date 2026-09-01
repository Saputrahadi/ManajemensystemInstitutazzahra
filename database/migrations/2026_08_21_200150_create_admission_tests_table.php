<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Jadwal ujian seleksi PMB per periode. */
    public function up(): void
    {
        Schema::create('admission_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_period_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->dateTime('test_date');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_tests');
    }
};
