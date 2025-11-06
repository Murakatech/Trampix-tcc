<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            'Restaurante',
            'Hotelaria',
            'Construção Civil',
            'Saúde',
            'Educação',
            'Tecnologia da Informação',
            'Marketing',
            'Design',
            'Varejo/Comércio',
            'Serviços Gerais',
            'Transporte/Logística',
            'Administração/Escritório',
            'Financeiro/Contábil',
            'Jurídico',
            'Agricultura/Agro',
            'Energia',
            'Mineração',
            'Turismo',
            'Beleza/Estética',
            'Eventos',
            'Segurança',
            'Limpeza',
            'Manutenção',
        ];

        foreach ($sectors as $name) {
            Sector::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => null,
                    'icon' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}