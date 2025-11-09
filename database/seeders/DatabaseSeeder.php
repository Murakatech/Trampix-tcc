<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Básico para iniciar do zero: segmentos e categorias
        $this->call([
            CategoryPerSegmentSeeder::class,
            AccountsSeeder::class,
            VacanciesSeeder::class,
            FeedbackSeeder::class,
        ]);
    }
}
