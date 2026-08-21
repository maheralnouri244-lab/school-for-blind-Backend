<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('past_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('numofquestions');
            $table->integer('timelimit');
            $table->integer('totalmark');
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->year('year');
            $table->enum('session', ['first', 'second', 'complementary'])->default('first');
            $table->string('voice_solution_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('past_exams');
    }
};