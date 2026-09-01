<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Keputusan atas satu grade_change_request — riwayat, mengikuti pola krs_approvals. */
    public function up(): void
    {
        Schema::create('grade_change_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_change_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->restrictOnDelete();
            $table->string('status'); // disetujui, ditolak
            $table->text('notes')->nullable();
            $table->timestamp('decided_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_change_approvals');
    }
};
