<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            $table->morphs('sender');

            $table->text('message');
            $table->string('attachment_path')->nullable();

            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('low');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');

            $table->enum('assigned_department', [
                'Super Admin',
                'Academic Manager',
                'Moderator',
                'Support Agent',
                'Data Entry',
                'Financial Manager'
            ])->nullable();
            $table->foreignId('classified_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};