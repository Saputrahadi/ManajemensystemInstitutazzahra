<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Permintaan koreksi nilai atas satu grade_finalization yang sudah terbit — mis. dosen salah input nilai. */
    public function up(): void
    {
        Schema::create('grade_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_finalization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->text('reason');
            $table->decimal('requested_score', 5, 2);
            $table->string('requested_letter_grade');
            $table->string('status')->default('menunggu'); // menunggu, disetujui, ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_change_requests');
    }
};
