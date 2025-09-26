<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adicionar novos campos na tabela freelancers
        Schema::table('freelancers', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('cv_url');
            $table->string('location')->nullable()->after('phone');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('location');
            $table->enum('availability', ['available', 'busy', 'unavailable'])->default('available')->after('hourly_rate');
            $table->boolean('is_active')->default(true)->after('availability');
        });

        // Adicionar novos campos na tabela companies
        Schema::table('companies', function (Blueprint $table) {
            $table->string('website')->nullable()->after('description');
            $table->string('phone')->nullable()->after('website');
            $table->integer('employees_count')->nullable()->after('phone');
            $table->year('founded_year')->nullable()->after('employees_count');
            $table->boolean('is_active')->default(true)->after('founded_year');
        });

        // Adicionar índices compostos para melhor performance
        Schema::table('freelancers', function (Blueprint $table) {
            $table->index(['user_id', 'is_active']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active']);
            $table->dropColumn(['phone', 'location', 'hourly_rate', 'availability', 'is_active']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active']);
            $table->dropColumn(['website', 'phone', 'employees_count', 'founded_year', 'is_active']);
        });
    }
};
