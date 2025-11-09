<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Segment;

class SegmentSeeder extends Seeder
{
    public function run(): void
    {
        $segments = [
            'Tecnologia e Informação',
            'Saúde e Bem-Estar',
            'Engenharia e Indústria',
        ];

        foreach ($segments as $name) {
            Segment::firstOrCreate(['name' => $name]);
        }
    }
}