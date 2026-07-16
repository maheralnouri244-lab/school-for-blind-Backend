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
$table->string('name');
$table->integer('timestamp_in_seconds');
$table->foreignId('student_id')->constrained()->onDelete('cascade');
$table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['lesson_id']);
            $table->dropColumn(['timestamp_in_seconds', 'student_id', 'lesson_id']);
        });
    }
};
