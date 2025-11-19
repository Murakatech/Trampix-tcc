<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\JobVacancy;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompaniesAndVacanciesSeeder extends Seeder
{
    public function run(): void
    {
        $segments = Segment::pluck('id', 'name');

        // EMPRESAS (SEM LOGOS)
        $companies = [

            [
                'name' => 'Restaurante Sabor Caseiro',
                'email' => 'restaurante@seed.trampix',
                'segments' => ['Gastronomia & Eventos'],
                'description' => 'Restaurante especializado em pratos caseiros e eventos.',
                'location' => 'Ribeirão Preto/SP',
                'cnpj' => '11.111.111/0001-11',
                'phone' => '(16) 3900-1001',
                'website' => 'https://saborcaseiro.com',
                'linkedin' => 'https://linkedin.com/company/saborcaseiro',
            ],

            [
                'name' => 'Buffet Prime Festas',
                'email' => 'buffet@seed.trampix',
                'segments' => ['Gastronomia & Eventos', 'Serviços Gerais & Operacionais'],
                'description' => 'Buffet completo para eventos e festas.',
                'location' => 'Sertãozinho/SP',
                'cnpj' => '22.222.222/0001-22',
                'phone' => '(16) 3500-2200',
                'website' => 'https://primefestas.com',
                'linkedin' => 'https://linkedin.com/company/primefestas',
            ],

            [
                'name' => 'ServManutenções Pro',
                'email' => 'servmanutencao@seed.trampix',
                'segments' => ['Serviços Gerais & Operacionais'],
                'description' => 'Especializada em manutenção elétrica, hidráulica e reformas.',
                'location' => 'Cravinhos/SP',
                'cnpj' => '33.333.333/0001-33',
                'phone' => '(16) 3200-3300',
                'website' => 'https://servmanutencoes.com',
                'linkedin' => 'https://linkedin.com/company/servmanutencoes',
            ],

            [
                'name' => 'Agência Criativa PixelUp',
                'email' => 'agenciacriativa@seed.trampix',
                'segments' => ['Criatividade, Mídia & Conteúdo'],
                'description' => 'Agência criativa especializada em branding e social media.',
                'location' => 'Ribeirão Preto/SP',
                'cnpj' => '44.444.444/0001-44',
                'phone' => '(16) 3300-4400',
                'website' => 'https://pixelup.com',
                'linkedin' => 'https://linkedin.com/company/pixelup',
            ],

            [
                'name' => 'DevTech Solutions',
                'email' => 'devtech@seed.trampix',
                'segments' => ['Tecnologia & Desenvolvimento'],
                'description' => 'Software house especializada em desenvolvimento web e apps.',
                'location' => 'Ribeirão Preto/SP',
                'cnpj' => '55.555.555/0001-55',
                'phone' => '(16) 3400-5500',
                'website' => 'https://devtech.com',
                'linkedin' => 'https://linkedin.com/company/devtech',
            ],
        ];

        foreach ($companies as $data) {

            // USER
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'display_name'      => $data['name'],
                    'role'              => 'company',
                    'password'          => Hash::make('Trampix@123'),
                    'email_verified_at' => now(),
                ]
            );

            // COMPANY (SEM LOGO)
            $company = Company::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'display_name'    => $data['name'],
                    'name'            => $data['name'],
                    'cnpj'            => $data['cnpj'],
                    'location'        => $data['location'],
                    'description'     => $data['description'],
                    'website'         => $data['website'],
                    'linkedin_url'    => $data['linkedin'],
                    'email'           => $data['email'],
                    'phone'           => $data['phone'],
                    'company_size'    => 'Pequeno',
                    'employees_count' => rand(5, 80),
                    'founded_year'    => rand(2005, 2022),
                    'is_active'       => true,
                ]
            );

            // SEGMENTOS
            $segmentIds = collect($data['segments'])
                ->map(fn($s) => $segments[$s] ?? null)
                ->filter()
                ->take(3)
                ->values()
                ->toArray();

            $company->segments()->sync($segmentIds);

            // CATEGORIAS
            $categories = Category::whereIn('segment_id', $segmentIds)->get();

            // VAGAS
            for ($i = 1; $i <= 5; $i++) {

                $cat = $categories->random();
                $title = $cat->name . " - " . $i;

                JobVacancy::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'title' => $title,
                    ],
                    [
                        'description' =>
                            "Profissional para atuar em {$cat->name}, com foco em qualidade e resultados.",
                        'requirements' =>
                            "Experiência comprovada como {$cat->name}, boa comunicação e comprometimento.",
                        'location_type' => $i % 2 === 0 ? 'Presencial' : 'Remoto',
                        'salary_min' => rand(1600, 3000),
                        'salary_max' => rand(3000, 6000),
                        'status' => 'active',
                        'category_id' => $cat->id,
                    ]
                );
            }
        }

        // ADMIN PADRÃO
        User::updateOrCreate(
            ['email' => 'admin@seed.trampix'],
            [
                'name'              => 'Admin Trampix',
                'display_name'      => 'Admin Trampix',
                'password'          => Hash::make('Trampix@123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
