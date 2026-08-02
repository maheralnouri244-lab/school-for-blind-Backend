<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('period_number');
            $table->timestamps();
            $table->unique(['teacher_id', 'day_of_week', 'period_number'], 'teacher_avail_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_availabilities');
    }
};