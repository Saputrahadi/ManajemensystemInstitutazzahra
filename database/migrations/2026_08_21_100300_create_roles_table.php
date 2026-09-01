<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // mis. super_admin, dekan_kaprodi, dosen, mahasiswa
            $table->string('name');
            $table->text('description')->nullable();
            // Role sistem (mis. Super Admin) tidak boleh dihapus/diubah lewat UI biasa —
            // prinsip "Technical Access != Business Authority".
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
