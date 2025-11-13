<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (! Schema::hasColumn('job_vacancies', 'service_category_id')) {
                $table->foreignId('service_category_id')
                    ->nullable()
                    ->constrained('service_categories')
                    ->nullOnDelete();
            }
        });

        // Backfill: map existing string categories to the corresponding ServiceCategory ID
        try {
            $map = DB::table('service_categories')->select('id', 'name')->get();
            foreach ($map as $row) {
                DB::table('job_vacancies')
                    ->where('category', $row->name)
                    ->update(['service_category_id' => $row->id]);
            }
        } catch (\Throwable $e) {
            // Log and continue; backfill is best-effort
            \Log::warning('Backfill service_category_id in job_vacancies failed: '.$e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (Schema::hasColumn('job_vacancies', 'service_category_id')) {
                $table->dropConstrainedForeignId('service_category_id');
            }
        });
    }
};
