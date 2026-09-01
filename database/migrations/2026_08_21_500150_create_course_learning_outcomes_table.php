<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** CPMK — pemetaan CPL ke Mata Kuliah tertentu (blueprint: "Pemetaan CPL->CPMK->MK"). */
    public function up(): void
    {
        Schema::create('course_learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_outcome_id')->constrained()->cascadeOnDelete();
            $table->string('code'); // mis. CPMK-01
            $table->text('description');
            $table->timestamps();

            $table->unique(['course_id', 'learning_outcome_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_learning_outcomes');
    }
};
