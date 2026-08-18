<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('punishment_objections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('caregiver_id')->constrained('caregivers')->cascadeOnDelete();

            $table->unsignedBigInteger('punishable_record_id');
            $table->foreign('punishable_record_id')->references('id')->on('punishables')->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('punishment_objections');
    }
};