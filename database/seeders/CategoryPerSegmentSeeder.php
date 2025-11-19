<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Segment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryPerSegmentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // 🍳 Gastronomia & Eventos
            'Gastronomia & Eventos' => [
                'Garçom',
                'Bartender',
                'Cozinheiro',
                'Chapeiro',
                'Auxiliar de Cozinha',
                'Confeiteiro',
                'Barista',
                'Buffet / Catering',
                'Auxiliar de Eventos',
                'Cerimonialista',
                'Recepcionista de Eventos',
            ],

            // 🔧 Serviços Gerais & Operacionais
            'Serviços Gerais & Operacionais' => [
                'Serviços Gerais',
                'Faxineira / Limpeza',
                'Jardineiro',
                'Diarista',
                'Pintor',
                'Montador de Móveis',
                'Eletricista',
                'Encanador',
                'Pedreiro',
                'Ajudante de Obra',
                'Técnico de Manutenção',
                'Transporte / Carreto',
                'Motoboy / Entregador',
            ],

            // 🛍 Comércio & Atendimento
            'Comércio & Atendimento' => [
                'Repositor',
                'Atendente de Loja',
                'Caixa',
                'Promotor de Vendas',
                'Vendedor',
                'Telemarketing',
                'Atendimento ao Cliente',
            ],

            // 🎨 Criatividade, Mídia & Conteúdo
            'Criatividade, Mídia & Conteúdo' => [
                'Designer Gráfico',
                'Fotógrafo',
                'Videomaker',
                'Editor de Vídeo',
                'Social Media',
                'Redator',
                'Criador de Conteúdo',
                'Motion Designer',
                'Gestão de Tráfego',
            ],

            // 💻 Tecnologia & Desenvolvimento
            'Tecnologia & Desenvolvimento' => [
                'Desenvolvedor Full Stack',
                'Desenvolvedor Backend',
                'Desenvolvedor Frontend',
                'Desenvolvedor Mobile',
                'Desenvolvedor WordPress / CMS',
                'UX/UI Designer',
                'Product Designer',
                'QA / Testes de Software',
                'Analista de Sistemas',
                'Automação / Scripts',
                'Administração de Servidores',
                'DevOps / CI-CD',
                'Banco de Dados / SQL',
                'Cibersegurança',
                'Infraestrutura / Redes',
                'Gerenciamento de Projetos (Tech)',
                'Gestão de Tráfego Pago (Google/Meta)',
                'Data Science / IA',
                'Machine Learning',
                'Suporte Técnico / Help Desk',
            ],

            // 💅 Saúde, Beleza & Bem-estar
            'Saúde, Beleza & Bem-estar' => [
                'Esteticista',
                'Manicure / Pedicure',
                'Maquiador',
                'Cabeleireiro',
                'Massagista',
                'Personal Trainer',
            ],

            // 📚 Educação & Especialistas
            'Educação & Especialistas' => [
                'Professor Particular',
                'Aulas de Idiomas',
                'Reforço Escolar',
                'Consultoria Especializada',
                'Instrutor Técnico',
            ],
        ];

        foreach ($data as $segmentName => $categories) {
            $segment = Segment::firstOrCreate(['name' => $segmentName]);

            foreach ($categories as $categoryName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    [
                        'name' => $categoryName,
                        'segment_id' => $segment->id,
                    ]
                );
            }
        }

        $this->command?->info('Segments and categories seeded successfully (freelancer-optimized version).');
    }
}
