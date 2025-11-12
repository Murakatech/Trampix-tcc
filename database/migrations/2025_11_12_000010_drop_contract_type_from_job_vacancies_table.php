<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('job_vacancies', 'contract_type')) {
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->dropColumn('contract_type');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('job_vacancies', 'contract_type')) {
            Schema::table('job_vacancies', function (Blueprint $table) {
                // Restaurar coluna com enum aproximado (compatível com MySQL)
                $table->enum('contract_type', ['PJ','CLT','Estágio','Freelance'])->nullable();
            });
        }
    }
};