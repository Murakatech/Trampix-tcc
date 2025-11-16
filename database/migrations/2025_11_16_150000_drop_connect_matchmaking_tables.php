<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('company_freelancer_recommendations');
        Schema::dropIfExists('freelancer_job_recommendations');
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('preferences');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Intencionalmente vazio: estrutura removida do sistema
    }
};