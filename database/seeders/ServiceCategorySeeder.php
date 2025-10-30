<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Desenvolvimento Web',
                'slug' => 'desenvolvimento-web',
                'description' => 'Criação de sites, aplicações web e sistemas online',
                'icon' => 'fas fa-code',
                'is_active' => true,
            ],
            [
                'name' => 'Design Gráfico',
                'slug' => 'design-grafico',
                'description' => 'Criação de identidade visual, logos e materiais gráficos',
                'icon' => 'fas fa-palette',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing Digital',
                'slug' => 'marketing-digital',
                'description' => 'Estratégias de marketing online, redes sociais e publicidade',
                'icon' => 'fas fa-bullhorn',
                'is_active' => true,
            ],
            [
                'name' => 'Redação e Copywriting',
                'slug' => 'redacao-copywriting',
                'description' => 'Criação de conteúdo, textos publicitários e artigos',
                'icon' => 'fas fa-pen',
                'is_active' => true,
            ],
            [
                'name' => 'Consultoria Empresarial',
                'slug' => 'consultoria-empresarial',
                'description' => 'Consultoria em gestão, processos e estratégia empresarial',
                'icon' => 'fas fa-chart-line',
                'is_active' => true,
            ],
            [
                'name' => 'Tradução',
                'slug' => 'traducao',
                'description' => 'Serviços de tradução e interpretação de idiomas',
                'icon' => 'fas fa-language',
                'is_active' => true,
            ],
            [
                'name' => 'Fotografia',
                'slug' => 'fotografia',
                'description' => 'Serviços fotográficos para eventos, produtos e retratos',
                'icon' => 'fas fa-camera',
                'is_active' => true,
            ],
            [
                'name' => 'Desenvolvimento Mobile',
                'slug' => 'desenvolvimento-mobile',
                'description' => 'Criação de aplicativos para dispositivos móveis',
                'icon' => 'fas fa-mobile-alt',
                'is_active' => true,
            ],
            [
                'name' => 'Análise de Dados',
                'slug' => 'analise-dados',
                'description' => 'Análise estatística, business intelligence e ciência de dados',
                'icon' => 'fas fa-chart-bar',
                'is_active' => true,
            ],
            [
                'name' => 'Suporte Técnico',
                'slug' => 'suporte-tecnico',
                'description' => 'Suporte técnico em TI, manutenção e helpdesk',
                'icon' => 'fas fa-tools',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }
    }
}
