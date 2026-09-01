<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Satu pertemuan kuliah dari sebuah Kelas (`classes`) — nomor pertemuan unik per kelas. */
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedTinyInteger('meeting_number');
            $table->string('topic')->nullable();
            $table->date('session_date');
            $table->string('status')->default('dijadwalkan'); // dijadwalkan, selesai, dibatalkan
            $table->timestamps();

            $table->unique(['class_id', 'meeting_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
