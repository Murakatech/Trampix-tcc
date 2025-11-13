<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('segments', function (Blueprint $table) {
            if (! Schema::hasColumn('segments', 'active')) {
                $table->boolean('active')->default(true)->after('name');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'active')) {
                $table->boolean('active')->default(true)->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('segments', function (Blueprint $table) {
            if (Schema::hasColumn('segments', 'active')) {
                $table->dropColumn('active');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
};
