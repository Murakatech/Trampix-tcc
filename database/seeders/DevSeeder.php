<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------------
        // ADMIN
        // --------------------------------------------------
        User::updateOrCreate(
            ['email' => 'admin@seed.trampix'],
            [
                'name' => 'Administrador',
                'display_name' => 'Administrador',
                'role' => 'admin',
                'password' => Hash::make('Trampix@123'),
                'email_verified_at' => now(),
            ]
        );

        // --------------------------------------------------
        // SEGMENTOS + CATEGORIAS (COMPLETO)
        // --------------------------------------------------
        $data = [

            'Gastronomia & Eventos' => [
                'Garçom', 'Cozinheiro', 'Chapeiro', 'Auxiliar de Cozinha',
                'Confeiteiro', 'Barista', 'Buffet / Catering'
            ],

            'Serviços Gerais & Operacionais' => [
                'Faxineira', 'Jardineiro', 'Diarista', 'Eletricista',
                'Encanador', 'Pedreiro', 'Montador de Móveis'
            ],

            'Tecnologia & Desenvolvimento' => [
                'Desenvolvedor Full Stack', 'Frontend', 'Backend', 'Mobile',
                'UX/UI', 'QA', 'Administração de Servidores', 'Banco de Dados'
            ],
        ];

        $segmentIds = [];

        foreach ($data as $segmentName => $cats) {

            $segment = Segment::updateOrCreate(
                ['name' => $segmentName],
                ['active' => true]
            );

            $segmentIds[$segmentName] = $segment->id;

            foreach ($cats as $categoryName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    [
                        'name' => $categoryName,
                        'segment_id' => $segment->id,
                        'active' => true
                    ]
                );
            }
        }

        // --------------------------------------------------
        // EMPRESA
        // --------------------------------------------------

        $companyName = "ServPlus Multisserviços";

        $companyUser = User::updateOrCreate(
            ['email' => 'servplus@seed.trampix'],
            [
                'name' => $companyName,
                'display_name' => $companyName,
                'role' => 'company',
                'password' => Hash::make('Trampix@123'),
                'email_verified_at' => now(),
            ]
        );

        $company = Company::updateOrCreate(
            ['user_id' => $companyUser->id],
            [
                'display_name' => $companyName,
                'name' => $companyName,
                'cnpj' => '99.999.999/0001-99',
                'location' => 'Ribeirão Preto/SP',
                'description' => 'Empresa multisserviços para gastronomia, manutenção e tecnologia.',
                'website' => 'https://servplus.com',
                'linkedin_url' => 'https://linkedin.com/company/servplus',
                'email' => 'servplus@seed.trampix',
                'phone' => '(16) 3400-9900',
                'company_size' => 'Médio',
                'employees_count' => 120,
                'founded_year' => 2018,
                'is_active' => true,
            ]
        );

        $company->segments()->sync(array_values($segmentIds));

        // --------------------------------------------------
        // CRIAR 10 VAGAS (AGORA GARANTIDAMENTE EXISTEM CATEGORIAS)
        // --------------------------------------------------

        $companySegmentIds = array_values($segmentIds);
        $catsForCompany = Category::whereIn('segment_id', $companySegmentIds)->get();
        $vacancies = [];

        for ($i = 1; $i <= 25; $i++) {

            $cat = $catsForCompany[$i % max(1, $catsForCompany->count())] ?? $catsForCompany->random();

            $vacancies[] = JobVacancy::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'title' => "{$cat->name} - Projeto {$i}",
                ],
                [
                    'description' => "Atuação como {$cat->name} em projeto interno.",
                    'requirements' => "Experiência comprovada como {$cat->name}.",
                    'location_type' => $i % 2 === 0 ? 'Remoto' : 'Presencial',
                    'salary_min' => rand(2000, 3500),
                    'salary_max' => rand(3500, 7000),
                    'status' => 'active',
                    'category_id' => $cat->id,
                ]
            );
        }

        // --------------------------------------------------
        // FREELANCERS
        // --------------------------------------------------

        $names = [
            'Ana Souza', 'João Carvalho', 'Patrícia Ramos',
            'Carlos Vieira', 'Marcos Lima', 'Fernanda Dias',
            'Lucas Teixeira', 'Rafael Lima', 'Thiago Martins', 'Beatriz Nunes'
        ];

        $freelancers = [];

        foreach ($names as $index => $name) {

            // segment alternado
            $segIndex = array_keys($segmentIds)[$index % count($segmentIds)];
            $segId = $segmentIds[$segIndex];

            $email = Str::slug($name) . '@seed.trampix';

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'display_name' => $name,
                    'role' => 'freelancer',
                    'password' => Hash::make('Trampix@123'),
                    'email_verified_at' => now(),
                ]
            );

            $freela = Freelancer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'display_name' => $name,
                    'bio' => "{$name} possui experiência no segmento {$segIndex}.",
                    'portfolio_url' => "https://portfolio.trampix.com/" . Str::slug($name),
                    'linkedin_url'  => "https://linkedin.com/in/" . Str::slug($name),
                    'cv_url'        => "https://cv.trampix.com/" . Str::slug($name) . ".pdf",
                    'whatsapp'      => '55119' . rand(900000000, 999999999),
                    'location'      => 'Ribeirão Preto/SP',
                    'hourly_rate'   => rand(60, 150),
                    'availability'  => 'project_based',
                    'is_active'     => true,
                    'segment_id'    => $segId,
                ]
            );

            $freela->segments()->sync([$segId]);
            $freelancers[] = $freela;
        }

        // --------------------------------------------------
        // APLICAÇÕES + AVALIAÇÕES
        // --------------------------------------------------

        foreach ($freelancers as $freela) {

            $cats = Category::where('segment_id', $freela->segment_id)->pluck('id');
            $jobs = JobVacancy::whereIn('category_id', $cats)->get();

            if ($jobs->isEmpty()) continue;

            $toApply = $jobs->shuffle()->take(3);

            foreach ($toApply as $job) {

                $app = Application::updateOrCreate(
                    [
                        'job_vacancy_id' => $job->id,
                        'freelancer_id' => $freela->id,
                    ],
                    [
                        'cover_letter' => "Tenho experiência prática como {$job->category->name}.",
                        'status' => 'applied',
                    ]
                );

                // histórico (1 a 2 finalizados por freela)
                for ($i = 0; $i < rand(1, 2); $i++) {

                    $done = $app->replicate();
                    $done->status = 'ended';

                    $ratingC = rand(4, 5);
                    $ratingF = rand(4, 5);

                    $done->company_rating = $ratingC;
                    $done->company_comment = "Ótimo trabalho realizado.";
                    $done->company_ratings_json = [
                        'qualidade' => $ratingC,
                        'prazo' => $ratingC,
                        'comunicacao' => $ratingC,
                    ];
                    $done->evaluated_by_company_at = now()->subDays(rand(5, 60));

                    $done->freelancer_rating = $ratingF;
                    $done->freelancer_comment = "Empresa organizada e profissional.";
                    $done->freelancer_ratings_json = [
                        'qualidade' => $ratingF,
                        'entrega' => $ratingF,
                        'profissionalismo' => $ratingF,
                    ];
                    $done->evaluated_by_freelancer_at = now()->subDays(rand(5, 60));

                    $done->save();
                }
            }
        }

        $this->command?->info("✔️ DevSeeder COMPLETO populado com sucesso!");
    }
}
