<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Kurikulum per Prodi — lihat blueprint #erd (domain Kurikulum) & relasi "Prodi -> Kurikulum -> Mata Kuliah". */
    public function up(): void
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('academic_year'); // tahun kurikulum ini mulai berlaku
            $table->unsignedSmallInteger('total_credits')->nullable();
            $table->string('status')->default('draft'); // draft, aktif, nonaktif
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curricula');
    }
};
