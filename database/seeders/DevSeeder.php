<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Application;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        $securePassword = 'Trampix@123'; // atende aos requisitos

        // ADMIN
        $admin = User::updateOrCreate(
            ['email' => 'admin@trampix.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make($securePassword),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | EMPRESAS DIVERSAS
        |--------------------------------------------------------------------------
        */
        $companiesData = [
            [
                'user' => ['email' => 'restaurante@trampix.com', 'name' => 'Restaurante Sabor & Arte'],
                'company' => [
                    'name' => 'Restaurante Sabor & Arte LTDA',
                    'sector' => 'Gastronomia',
                    'location' => 'São Paulo/SP',
                    'description' => 'Restaurante contemporâneo com foco em culinária brasileira e ingredientes regionais.',
                ],
                'jobs' => [
                    [
                        'title' => 'Cozinheiro Pleno',
                        'category' => 'Cozinha',
                        'description' => 'Preparo de pratos e organização da cozinha.',
                        'contract_type' => 'CLT',
                        'salary_range' => 'R$ 2.500 - R$ 3.200',
                        'requirements' => 'Experiência prévia, conhecimento em manipulação de alimentos.'
                    ],
                    [
                        'title' => 'Garçom',
                        'category' => 'Atendimento',
                        'description' => 'Atendimento aos clientes e anotações de pedidos.',
                        'contract_type' => 'CLT',
                        'salary_range' => 'R$ 1.800 - R$ 2.200',
                        'requirements' => 'Boa comunicação e agilidade.'
                    ]
                ]
            ],
            [
                'user' => ['email' => 'agencia@trampix.com', 'name' => 'Agência Criarte'],
                'company' => [
                    'name' => 'Agência Criarte Comunicação LTDA',
                    'sector' => 'Marketing e Design',
                    'location' => 'Rio de Janeiro/RJ',
                    'description' => 'Agência especializada em branding, marketing digital e design de identidade visual.',
                ],
                'jobs' => [
                    [
                        'title' => 'Designer Gráfico',
                        'category' => 'Design',
                        'description' => 'Criação de logotipos e materiais gráficos para campanhas publicitárias.',
                        'contract_type' => 'PJ',
                        'salary_range' => 'R$ 3.000 - R$ 4.500',
                        'requirements' => 'Domínio de Adobe Illustrator e Photoshop.'
                    ],
                    [
                        'title' => 'Social Media',
                        'category' => 'Marketing',
                        'description' => 'Gestão de redes sociais e criação de conteúdo digital.',
                        'contract_type' => 'PJ',
                        'salary_range' => 'R$ 2.000 - R$ 3.000',
                        'requirements' => 'Experiência com ferramentas de agendamento e análise de métricas.'
                    ]
                ]
            ],
            [
                'user' => ['email' => 'oficina@trampix.com', 'name' => 'Oficina Mecânica TurboCar'],
                'company' => [
                    'name' => 'TurboCar Serviços Automotivos',
                    'sector' => 'Mecânica Automotiva',
                    'location' => 'Belo Horizonte/MG',
                    'description' => 'Oficina especializada em manutenção de veículos nacionais e importados.',
                ],
                'jobs' => [
                    [
                        'title' => 'Mecânico Automotivo',
                        'category' => 'Serviços',
                        'description' => 'Diagnóstico e reparo de motores, freios e suspensão.',
                        'contract_type' => 'CLT',
                        'salary_range' => 'R$ 2.800 - R$ 4.000',
                        'requirements' => 'Experiência em manutenção automotiva e carteira de motorista B.'
                    ],
                    [
                        'title' => 'Atendente de Oficina',
                        'category' => 'Atendimento',
                        'description' => 'Receber clientes, abrir ordens de serviço e acompanhar agendamentos.',
                        'contract_type' => 'CLT',
                        'salary_range' => 'R$ 1.700 - R$ 2.200',
                        'requirements' => 'Boa comunicação e organização.'
                    ]
                ]
            ],
            [
                'user' => ['email' => 'hotel@trampix.com', 'name' => 'Hotel Bela Vista'],
                'company' => [
                    'name' => 'Hotel Bela Vista LTDA',
                    'sector' => 'Hotelaria',
                    'location' => 'Gramado/RS',
                    'description' => 'Hotel de luxo com atendimento personalizado e gastronomia de alto padrão.',
                ],
                'jobs' => [
                    [
                        'title' => 'Recepcionista de Hotel',
                        'category' => 'Atendimento',
                        'description' => 'Recepção de hóspedes e controle de reservas.',
                        'contract_type' => 'CLT',
                        'salary_range' => 'R$ 2.200 - R$ 2.800',
                        'requirements' => 'Boa comunicação e inglês intermediário.'
                    ],
                    [
                        'title' => 'Camareira',
                        'category' => 'Serviços Gerais',
                        'description' => 'Limpeza e organização dos quartos e áreas comuns.',
                        'contract_type' => 'CLT',
                        'salary_range' => 'R$ 1.600 - R$ 2.000',
                        'requirements' => 'Experiência anterior e atenção aos detalhes.'
                    ]
                ]
            ]
        ];

        foreach ($companiesData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                [
                    'name' => $data['user']['name'],
                    'password' => Hash::make($securePassword),
                    'role' => 'company',
                    'email_verified_at' => now(),
                ]
            );

            $company = Company::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data['company'], [
                    'display_name' => $data['company']['name'], // Nome de exibição da empresa
                    'cnpj' => fake()->numerify('##.###.###/0001-##'),
                    'phone' => fake()->phoneNumber(),
                    'website' => 'https://' . strtolower(str_replace(' ', '', $data['company']['name'])) . '.com',
                    'employees_count' => rand(5, 50),
                    'founded_year' => rand(2010, 2022),
                    'is_active' => true,
                ])
            );

            foreach ($data['jobs'] as $job) {
                JobVacancy::updateOrCreate(
                    ['company_id' => $company->id, 'title' => $job['title']],
                    array_merge($job, [
                        'company_id' => $company->id,
                        'status' => 'active',
                        'location_type' => 'Presencial',
                        'created_at' => now()->subDays(rand(1, 30)),
                    ])
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FREELANCERS E CANDIDATURAS
        |--------------------------------------------------------------------------
        */
        $freelancerUser = User::updateOrCreate(
            ['email' => 'freelancer@trampix.com'],
            [
                'name' => 'Ana Rodrigues',
                'password' => Hash::make($securePassword),
                'role' => 'freelancer',
                'email_verified_at' => now(),
            ]
        );

        $freelancer = Freelancer::updateOrCreate(
            ['user_id' => $freelancerUser->id],
            [
                'display_name' => 'Ana Rodrigues - Designer & Freelancer', // Nome de exibição do freelancer
                'bio' => 'Profissional autônoma com experiência em design, atendimento e gastronomia.',
                'portfolio_url' => 'https://anarodrigues.dev',
                'phone' => '(11) 99999-9999',
                'location' => 'São Paulo/SP',
                'hourly_rate' => 85.00,
                'availability' => 'Disponível para trabalhos pontuais',
                'is_active' => true,
            ]
        );

        $randomJobs = JobVacancy::inRandomOrder()->take(3)->get();
        foreach ($randomJobs as $job) {
            Application::updateOrCreate(
                [
                    'job_vacancy_id' => $job->id,
                    'freelancer_id' => $freelancer->id,
                ],
                [
                    'cover_letter' => 'Tenho experiência na área e disponibilidade imediata.',
                    'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
                    'created_at' => now()->subDays(rand(1, 15)),
                ]
            );
        }

        echo "✅ DevSeeder executado com sucesso!\n";
        echo "📧 Admin: admin@trampix.com | Senha: Trampix@123\n";
        echo "👤 Freelancer: freelancer@trampix.com | Senha: Trampix@123\n";
        echo "🏢 Empresas criadas com mesma senha de acesso.\n";
        echo "📊 Vagas e candidaturas criadas automaticamente.\n";
    }
}
