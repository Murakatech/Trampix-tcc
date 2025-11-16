<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connect_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liker_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['freelancer', 'company']);
            $table->enum('target_type', ['job', 'freelancer']);
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('job_vacancy_id')->nullable();
            $table->unsignedBigInteger('freelancer_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['role', 'target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connect_likes');
    }
};