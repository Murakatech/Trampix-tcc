<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'website')) {
                $table->string('website')->nullable()->after('description');
            }
            if (!Schema::hasColumn('companies', 'phone')) {
                $table->string('phone', 20)->nullable()->after('website');
            }
            if (!Schema::hasColumn('companies', 'employees_count')) {
                $table->unsignedInteger('employees_count')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('companies', 'founded_year')) {
                $table->unsignedSmallInteger('founded_year')->nullable()->after('employees_count');
            }
            if (!Schema::hasColumn('companies', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('founded_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('companies', 'founded_year')) {
                $table->dropColumn('founded_year');
            }
            if (Schema::hasColumn('companies', 'employees_count')) {
                $table->dropColumn('employees_count');
            }
            if (Schema::hasColumn('companies', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('companies', 'website')) {
                $table->dropColumn('website');
            }
        });
    }
};