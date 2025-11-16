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
        $segmentNames = [
            'Tecnologia e Informação',
            'Negócios e Administração',
            'Comunicação e Criatividade',
        ];

        $companiesData = [
            [
                'user' => ['name' => 'Tech Corp', 'email' => 'techcorp@seed.trampix'],
                'company' => [
                    'display_name' => 'Tech Corp',
                    'name' => 'Tech Corp Tecnologia LTDA',
                    'cnpj' => '00.000.000/0001-01',
                    'location' => 'São Paulo/SP',
                    'description' => 'Empresa focada em produtos digitais e inovação.',
                    'website' => 'https://techcorp.example.com',
                    'linkedin_url' => 'https://www.linkedin.com/company/techcorp',
                    'email' => 'contato@techcorp.example.com',
                    'phone' => '+55 11 3000-0001',
                    'company_size' => 'Médio',
                    'employees_count' => 250,
                    'founded_year' => 2012,
                    'is_active' => true,
                ],
            ],
            [
                'user' => ['name' => 'Biz Solutions', 'email' => 'bizsolutions@seed.trampix'],
                'company' => [
                    'display_name' => 'Biz Solutions',
                    'name' => 'Biz Solutions Consultoria S/A',
                    'cnpj' => '00.000.000/0001-02',
                    'location' => 'Rio de Janeiro/RJ',
                    'description' => 'Consultoria empresarial e estratégia.',
                    'website' => 'https://bizsolutions.example.com',
                    'linkedin_url' => 'https://www.linkedin.com/company/bizsolutions',
                    'email' => 'contato@bizsolutions.example.com',
                    'phone' => '+55 21 3000-0002',
                    'company_size' => 'Grande',
                    'employees_count' => 480,
                    'founded_year' => 2008,
                    'is_active' => true,
                ],
            ],
            [
                'user' => ['name' => 'Creative Labs', 'email' => 'creativelabs@seed.trampix'],
                'company' => [
                    'display_name' => 'Creative Labs',
                    'name' => 'Creative Labs Comunicação ME',
                    'cnpj' => '00.000.000/0001-03',
                    'location' => 'Curitiba/PR',
                    'description' => 'Estúdio criativo para marcas e conteúdo.',
                    'website' => 'https://creativelabs.example.com',
                    'linkedin_url' => 'https://www.linkedin.com/company/creativelabs',
                    'email' => 'contato@creativelabs.example.com',
                    'phone' => '+55 41 3000-0003',
                    'company_size' => 'Pequeno',
                    'employees_count' => 45,
                    'founded_year' => 2016,
                    'is_active' => true,
                ],
            ],
        ];

        foreach ($segmentNames as $idx => $segmentName) {
            $segment = Segment::where('name', $segmentName)->first();
            if (! $segment) {
                continue;
            }

            $userData = $companiesData[$idx]['user'];
            $companyData = $companiesData[$idx]['company'];

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'display_name' => $userData['name'],
                    'password' => Hash::make('Trampix@123'),
                    'email_verified_at' => now(),
                    'role' => 'company',
                ]
            );

                $company = Company::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($companyData, [
                    'segment_id' => $segment->id,
                ])
            );

            $company->segments()->sync([$segment->id]);
            // Vincular setor normalizado
            $sector = \App\Models\Sector::firstOrCreate(['name' => $companyData['sector'] ?? $segment->name], [
                'slug' => \Illuminate\Support\Str::slug($companyData['sector'] ?? $segment->name),
                'is_active' => true,
            ]);
            try { $company->sectors()->syncWithoutDetaching([$sector->id]); } catch (\Throwable $e) {}

            $categories = Category::where('segment_id', $segment->id)->orderBy('id')->get();
            $total = 10;
            for ($i = 0; $i < $total; $i++) {
                $cat = $categories->count() > 0 ? $categories[$i % $categories->count()] : null;
                $title = $cat ? ($cat->name.' '.($i + 1)) : ('Vaga '.($i + 1));
                JobVacancy::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'title' => $title,
                    ],
                    [
                        'description' => 'Projeto para '.$title.' com escopo completo.',
                        'requirements' => 'Experiência comprovada em '.($cat ? $cat->name : 'área relacionada').'.',
                        'location_type' => $i % 2 === 0 ? 'Remoto' : 'Híbrido',
                        'salary_min' => (4000 + $i * 300),
                        'salary_max' => (7000 + $i * 300),
                        'status' => 'active',
                        'category_id' => $cat ? $cat->id : null,
                    ]
                );
            }
        }

        User::updateOrCreate(
            ['email' => 'admin@seed.trampix'],
            [
                'name' => 'Admin Trampix',
                'display_name' => 'Admin Trampix',
                'password' => Hash::make('Trampix@123'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
    }
}