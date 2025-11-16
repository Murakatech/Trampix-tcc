<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connect_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('freelancers')->cascadeOnDelete();
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['freelancer_id', 'job_vacancy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connect_matches');
    }
};