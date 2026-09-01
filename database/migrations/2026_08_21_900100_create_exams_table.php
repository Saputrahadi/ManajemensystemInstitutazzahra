<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** UTS/UAS untuk satu Kelas — satu kelas cuma boleh punya satu UTS dan satu UAS. */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('type'); // uts, uas
            $table->string('title');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedTinyInteger('max_score')->default(100);
            $table->string('status')->default('dijadwalkan'); // dijadwalkan, selesai, dibatalkan
            $table->timestamps();

            $table->unique(['class_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
