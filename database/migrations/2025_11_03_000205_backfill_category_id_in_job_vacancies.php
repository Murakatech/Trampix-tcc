<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Preencher job_vacancies.category_id com base no nome em job_vacancies.category, quando possível
        $vacancies = DB::table('job_vacancies')
            ->select('id', 'category', 'category_id')
            ->whereNull('category_id')
            ->whereNotNull('category')
            ->get();

        if ($vacancies->isEmpty()) {
            return;
        }

        // Mapa de nome -> id de categories
        $categoriesMap = DB::table('categories')->pluck('id', 'name');

        foreach ($vacancies as $vaga) {
            $name = trim($vaga->category ?? '');
            if ($name === '') continue;

            // Tentativa 1: correspondência direta
            $catId = $categoriesMap[$name] ?? null;

            // Tentativa 2: correspondência case-insensitive
            if (!$catId) {
                $lowerMap = collect($categoriesMap)->mapWithKeys(function($id, $n) {
                    return [mb_strtolower(trim($n)) => $id];
                });
                $catId = $lowerMap[mb_strtolower($name)] ?? null;
            }

            if ($catId) {
                DB::table('job_vacancies')
                    ->where('id', $vaga->id)
                    ->update(['category_id' => $catId]);
            }
        }
    }

    public function down(): void
    {
        // Reverte o backfill limpando category_id onde foi preenchido a partir de category
        DB::table('job_vacancies')
            ->whereNotNull('category_id')
            ->update(['category_id' => null]);
    }
};