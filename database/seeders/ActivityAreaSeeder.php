<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityArea;

class ActivityAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['name' => 'Tecnologia da Informação', 'type' => null, 'description' => null],
            ['name' => 'Design e Criatividade', 'type' => null, 'description' => null],
            ['name' => 'Marketing e Comunicação', 'type' => null, 'description' => null],
            ['name' => 'Vendas e Comercial', 'type' => null, 'description' => null],
            ['name' => 'Recursos Humanos', 'type' => null, 'description' => null],
            ['name' => 'Finanças e Contabilidade', 'type' => null, 'description' => null],
            ['name' => 'Jurídico', 'type' => null, 'description' => null],
            ['name' => 'Operações e Logística', 'type' => null, 'description' => null],
            ['name' => 'Educação e Treinamento', 'type' => null, 'description' => null],
            ['name' => 'Saúde', 'type' => null, 'description' => null],
        ];

        foreach ($areas as $area) {
            ActivityArea::firstOrCreate(['name' => $area['name']], $area);
        }
    }
}