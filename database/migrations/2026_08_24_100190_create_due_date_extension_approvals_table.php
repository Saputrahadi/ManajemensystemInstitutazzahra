<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Keputusan pimpinan atas satu due_date_extension — riwayat append-only, pola sama dengan krs_approvals/grade_change_approvals. */
    public function up(): void
    {
        Schema::create('due_date_extension_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('due_date_extension_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->restrictOnDelete();
            $table->string('status'); // disetujui, ditolak
            $table->text('notes')->nullable();
            $table->timestamp('decided_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('due_date_extension_approvals');
    }
};
