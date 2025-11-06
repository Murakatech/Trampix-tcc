<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'activity_area_id')) {
                $table->foreignId('activity_area_id')
                    ->nullable()
                    ->constrained('activity_areas')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'activity_area_id')) {
                $table->dropConstrainedForeignId('activity_area_id');
            }
        });
    }
};