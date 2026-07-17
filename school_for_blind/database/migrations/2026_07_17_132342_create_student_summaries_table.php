<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('type', ['daily', 'monthly']);
            $table->string('reference_date');
            $table->json('data');

            $table->timestamps();
            $table->unique(['student_id', 'type', 'reference_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_summaries');
    }
};