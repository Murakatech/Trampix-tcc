<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Segment;
use App\Models\Category;

class CategoryPerSegmentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // 💼 Negócios e Administração
            'Negócios e Administração' => [
                'Administração',
                'Contabilidade',
                'Finanças',
                'Recursos Humanos',
                'Marketing',
                'Vendas e Comercial',
                'Empreendedorismo',
                'Comércio Exterior',
                'Logística',
                'Consultoria Empresarial',
            ],

            // 💻 Tecnologia e Informação
            'Tecnologia e Informação' => [
                'Desenvolvimento de Software',
                'Análise de Sistemas',
                'Suporte Técnico / Help Desk',
                'Banco de Dados',
                'Cibersegurança',
                'UX/UI Design',
                'Inteligência Artificial',
                'Ciência de Dados',
                'Infraestrutura e Redes',
                'Game Design',
            ],

            // 🏗️ Engenharia e Indústria
            'Engenharia e Indústria' => [
                'Engenharia Civil',
                'Engenharia Mecânica',
                'Engenharia Elétrica',
                'Engenharia de Produção',
                'Engenharia Ambiental',
                'Engenharia Química',
                'Manutenção Industrial',
                'Desenho Técnico / CAD',
            ],

            // 🎨 Comunicação e Criatividade
            'Comunicação e Criatividade' => [
                'Design Gráfico',
                'Publicidade e Propaganda',
                'Jornalismo',
                'Fotografia',
                'Produção Audiovisual',
                'Moda',
                'Redação e Copywriting',
                'Social Media',
                'Edição de Vídeo',
            ],

            // 🧑‍⚕️ Saúde e Bem-Estar
            'Saúde e Bem-Estar' => [
                'Enfermagem',
                'Medicina',
                'Psicologia',
                'Fisioterapia',
                'Nutrição',
                'Educação Física',
                'Estética e Beleza',
            ],

            // 🏫 Educação e Pesquisa
            'Educação e Pesquisa' => [
                'Pedagogia',
                'Letras',
                'Ensino de Idiomas',
                'Pesquisa Acadêmica',
                'Tutoria / Aulas particulares',
            ],

            // ⚖️ Jurídico e Público
            'Jurídico e Público' => [
                'Direito',
                'Advocacia',
                'Administração Pública',
                'Contabilidade Pública',
                'Gestão Governamental',
            ],

            // 🌱 Meio Ambiente e Sustentabilidade
            'Meio Ambiente e Sustentabilidade' => [
                'Gestão Ambiental',
                'Agricultura / Agronegócio',
                'Biotecnologia',
                'Energias Renováveis',
            ],

            // 🧱 Serviços e Operações
            'Serviços e Operações' => [
                'Construção Civil',
                'Transporte e Logística',
                'Serviços Gerais',
                'Limpeza e Conservação',
                'Segurança Patrimonial',
                'Atendimento ao Cliente',
            ],

            // 🛍️ Comércio e Atendimento
            'Comércio e Atendimento' => [
                'Varejo',
                'E-commerce',
                'Atendimento ao Cliente',
                'Telemarketing',
                'Representação Comercial',
            ],
        ];

        foreach ($data as $segmentName => $categories) {
            $segment = Segment::firstOrCreate(['name' => $segmentName]);

            foreach ($categories as $categoryName) {
                $slug = Str::slug($categoryName);
                Category::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $categoryName,
                        'segment_id' => $segment->id,
                    ]
                );
            }
        }

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('Segments and categories seeded successfully.');
        }
    }
}
