<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar colunas necessárias se não existirem e garantir NOT NULL para whatsapp
        Schema::table('freelancers', function (Blueprint $t) {
            if (!Schema::hasColumn('freelancers', 'display_name')) {
                $t->string('display_name', 255)->after('user_id');
            }
            if (!Schema::hasColumn('freelancers', 'whatsapp')) {
                // adicionar com valor padrão para evitar falha em tabelas existentes
                $t->string('whatsapp', 20)->default('')->after('portfolio_url');
            }
        });

        // Forçar NOT NULL nas colunas relevantes (MySQL)
        try {
            if (Schema::hasColumn('freelancers', 'whatsapp')) {
                DB::statement('ALTER TABLE freelancers MODIFY whatsapp VARCHAR(20) NOT NULL');
            }
            if (Schema::hasColumn('freelancers', 'display_name')) {
                DB::statement('ALTER TABLE freelancers MODIFY display_name VARCHAR(255) NOT NULL');
            }
        } catch (\Throwable $e) {
            // Em alguns ambientes, a modificação pode exigir doctrine/dbal.
            // Mantemos a coluna existente; a validação de aplicação continuará obrigatória.
        }
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $t) {
            if (Schema::hasColumn('freelancers', 'whatsapp')) {
                $t->string('whatsapp', 20)->nullable()->change();
            }
            if (Schema::hasColumn('freelancers', 'display_name')) {
                $t->string('display_name', 255)->nullable()->change();
            }
        });
    }
};