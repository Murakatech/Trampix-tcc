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
        // Primeiros: segmentos e categorias por segmento
        $this->call([
            SegmentSeeder::class,
            CategoryPerSegmentSeeder::class,
        ]);

        // Demais dados de desenvolvimento
        $this->call(DevSeeder::class);
    }
}
