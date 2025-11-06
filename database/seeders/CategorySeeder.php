<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // If ServiceCategory table exists and has data, copy into categories for continuity
        try {
            $serviceCategories = ServiceCategory::all();
            foreach ($serviceCategories as $sc) {
                Category::firstOrCreate(
                    ['slug' => $sc->slug ?: Str::slug($sc->name)],
                    [
                        'name' => $sc->name,
                        'description' => $sc->description,
                    ]
                );
            }
        } catch (\Throwable $e) {
            // Fallback: seed a minimal set if ServiceCategory is not available
            $defaults = [
                'Desenvolvimento Web',
                'Design Gráfico',
                'Marketing Digital',
                'Redação & Conteúdo',
                'Suporte de TI'
            ];
            foreach ($defaults as $name) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'name' => $name,
                        'description' => null,
                    ]
                );
            }
        }
    }
}