<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('bookmarks', function (Blueprint $table) {
    $table->id();
    $table->string('name')->nullable();
    $table->integer('timestamp_in_seconds');
    $table->foreignId('student_id')->constrained()->onDelete('cascade');
    $table->foreignId('record_id')->constrained('records')->onDelete('cascade');
    $table->timestamps();
    $table->unique(['student_id', 'record_id', 'timestamp_in_seconds'], 'student_record_timestamp_unique');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('bookmarks', function (Blueprint $table) {
        Schema::dropIfExists('bookmarks');
        });
    }
};
