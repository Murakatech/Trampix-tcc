<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (!Schema::hasColumn('job_vacancies', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->constrained('categories')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (Schema::hasColumn('job_vacancies', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });
    }
};