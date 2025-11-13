<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('description');
            $t->text('requirements')->nullable();
            $t->string('category')->nullable();
            $t->enum('contract_type', ['PJ', 'CLT', 'Estágio', 'Freelance'])->nullable();
            $t->enum('location_type', ['Remoto', 'Híbrido', 'Presencial'])->nullable();
            $t->string('salary_range')->nullable();
            $t->enum('status', ['active', 'closed'])->default('active');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
