<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penugasan role ke pengguna, dengan scoping unit opsional per-baris.
     *
     * Inilah mekanisme data-scoping inti: baris {user: Kaprodi A, role: dekan_kaprodi,
     * unit: Prodi A} membuat Kaprodi tersebut hanya berwenang atas data Prodi A —
     * bukan lewat kode keras (hardcode), tapi lewat data penugasan ini.
     * unit_id null berarti role berlaku institut-wide (mis. Super Admin, Rektor).
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('organizational_units')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Catatan PostgreSQL: NULL dianggap berbeda satu sama lain oleh unique index,
            // jadi baris unit_id=null (role institut-wide) tidak sepenuhnya dijaga oleh
            // constraint ini — validasi duplikasi role institut-wide dilakukan di Service layer.
            $table->unique(['user_id', 'role_id', 'unit_id'], 'user_roles_unique_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
