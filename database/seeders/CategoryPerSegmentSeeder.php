<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Segment;

class CategoryPerSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapa exemplo; substituiremos com a lista completa fornecida posteriormente
        $map = [
            'Tecnologia e Informação' => [
                'Desenvolvimento de Software',
                'Análise de Sistemas',
                'Infraestrutura de TI',
            ],
            'Saúde e Bem-Estar' => [
                'Enfermagem',
                'Fisioterapia',
                'Nutrição',
            ],
            'Engenharia e Indústria' => [
                'Engenharia Civil',
                'Engenharia Mecânica',
                'Produção Industrial',
            ],
        ];

        foreach ($map as $segmentName => $categories) {
            $segment = Segment::firstOrCreate(['name' => $segmentName]);

            foreach ($categories as $catName) {
                $slug = Str::slug($catName);
                $category = Category::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $catName]
                );

                // Vincular ou atualizar o segment_id da categoria
                if ($category->segment_id !== $segment->id) {
                    $category->segment_id = $segment->id;
                    $category->save();
                }
            }
        }

        // Opcional: associar categorias legadas já existentes a um segmento padrão se necessário
        // (mantemos assim para não quebrar dados atuais)
    }
}