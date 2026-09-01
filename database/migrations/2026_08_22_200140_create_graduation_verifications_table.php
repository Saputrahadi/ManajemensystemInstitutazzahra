<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Verifikasi pemenuhan syarat kelulusan seorang mahasiswa terhadap graduation_requirements prodinya. */
    public function up(): void
    {
        Schema::create('graduation_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('graduation_requirement_id')->constrained()->restrictOnDelete();
            $table->boolean('meets_requirements');
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('verified_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_verifications');
    }
};
