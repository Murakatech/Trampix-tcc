<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill category_id using existing text in job_vacancies.category
        if (Schema::hasTable('job_vacancies') && Schema::hasColumn('job_vacancies', 'category') && Schema::hasColumn('job_vacancies', 'category_id')) {
            $distinct = DB::table('job_vacancies')
                ->select(DB::raw('DISTINCT category'))
                ->whereNotNull('category')
                ->where('category', '<>', '')
                ->pluck('category');

            foreach ($distinct as $name) {
                $trimmed = trim((string) $name);
                if ($trimmed === '') continue;
                $existing = DB::table('categories')->where('name', $trimmed)->first();
                if (! $existing) {
                    $slug = Str::slug($trimmed);
                    // Garantir unicidade de slug
                    $base = $slug !== '' ? $slug : Str::slug('categoria');
                    $slugFinal = $base;
                    $i = 1;
                    while (DB::table('categories')->where('slug', $slugFinal)->exists()) {
                        $slugFinal = $base.'-'.$i;
                        $i++;
                    }
                    DB::table('categories')->insert([
                        'name' => $trimmed,
                        'slug' => $slugFinal,
                        'description' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $existing = DB::table('categories')->where('slug', $slugFinal)->first();
                }
                if ($existing) {
                    DB::table('job_vacancies')
                        ->where('category', $trimmed)
                        ->update(['category_id' => $existing->id]);
                }
            }

            // Após o backfill, remover coluna textual redundante
            Schema::table('job_vacancies', function (Blueprint $table) {
                if (Schema::hasColumn('job_vacancies', 'category')) {
                    $table->dropColumn('category');
                }
            });
        }
    }

    public function down(): void
    {
        // Restaurar coluna textual caso seja necessário
        if (Schema::hasTable('job_vacancies') && ! Schema::hasColumn('job_vacancies', 'category')) {
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->string('category')->nullable()->after('requirements');
            });
        }
    }
};

