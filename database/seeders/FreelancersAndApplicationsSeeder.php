<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FreelancersAndApplicationsSeeder extends Seeder
{
    public function run(): void
    {
        $segments = Segment::orderBy('id')->get();
        if ($segments->count() === 0) {
            return;
        }

        $names = [
            'Ana Souza',
            'Bruno Lima',
            'Carla Mendes',
            'Diego Almeida',
            'Eduarda Pires',
            'Felipe Martins',
            'Gabriela Rocha',
            'Henrique Santos',
            'Isabela Nogueira',
            'João Pedro Costa',
        ];
        $locations = [
            'São Paulo/SP',
            'Rio de Janeiro/RJ',
            'Belo Horizonte/MG',
            'Curitiba/PR',
            'Porto Alegre/RS',
            'Recife/PE',
            'Salvador/BA',
            'Florianópolis/SC',
            'Fortaleza/CE',
            'Brasília/DF',
        ];
        $rates = [120, 95, 110, 100, 130, 85, 140, 105, 115, 125];
        $bios = [
            'Experiência prática e foco em resultados.',
            'Portfólio sólido e entregas consistentes.',
            'Metodologias ágeis e comunicação clara.',
            'Pesquisa aplicada e melhoria contínua.',
            'Design centrado no usuário e testes.',
            'Estratégia de marca e conteúdo.',
            'Otimização de processos e métricas.',
            'Análise de dados e insights acionáveis.',
            'Storytelling e experiência digital.',
            'Colaboração multidisciplinar e inovação.',
        ];

        $freelancers = [];
        for ($i = 0; $i < 10; $i++) {
            $seg = $segments[$i % $segments->count()];
            $name = $names[$i];
            $slug = Str::slug($name);
            $email = $slug.'@seed.trampix';

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'display_name' => $name,
                    'password' => Hash::make('Trampix@123'),
                    'email_verified_at' => now(),
                    'role' => 'freelancer',
                ]
            );

            $freelancer = Freelancer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'display_name' => $name,
                    'bio' => 'Profissional de '.$seg->name.'. '.$bios[$i],
                    'portfolio_url' => 'https://portfolio.example.com/'.$slug,
                    'linkedin_url' => 'https://www.linkedin.com/in/'.$slug,
                    'cv_url' => 'https://cv.example.com/'.$slug.'.pdf',
                    'whatsapp' => '55119'.str_pad((string) (870000 + $i), 7, '0', STR_PAD_LEFT),
                    'location' => $locations[$i],
                    'hourly_rate' => $rates[$i],
                    'availability' => 'project_based',
                    'is_active' => true,
                    'segment_id' => $seg->id,
                ]
            );
            $freelancer->segments()->sync([$seg->id]);
            $freelancers[] = $freelancer;
        }

        $jobs = JobVacancy::query()->orderBy('id')->get();
        if ($jobs->count() === 0) {
            return;
        }

        foreach ($freelancers as $idx => $freelancer) {
            $applyCount = 4 + ($idx % 2);
            $selected = $jobs->shuffle()->take($applyCount);
            foreach ($selected as $job) {
                Application::updateOrCreate(
                    [
                        'job_vacancy_id' => $job->id,
                        'freelancer_id' => $freelancer->id,
                    ],
                    [
                        'cover_letter' => 'Olá, sou '.$freelancer->display_name.' com experiência em '.$seg->name.' e posso contribuir no projeto '.$job->title.'.',
                        'status' => 'applied',
                    ]
                );
            }
        }
    }
}