<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use Illuminate\Database\Seeder;

class PartnershipsAndEvaluationsSeeder extends Seeder
{
    public function run(): void
    {
        // DESTQUES
        $superFreelancerName = "Lucas Teixeira";
        $superCompanyName = "DevTech Solutions";

        $superFreelancer = Freelancer::whereHas('user', fn($q) =>
            $q->where('name', $superFreelancerName)
        )->first();

        $superCompany = Company::where('display_name', $superCompanyName)->first();

        if (!$superFreelancer || !$superCompany) {
            $this->command?->error("Não encontrei Lucas Teixeira ou DevTech. Rode os outros seeders primeiro!");
            return;
        }

        // LISTAS DE AVALIAÇÕES DETALHADAS
        $companyComments = [
            "Excelente qualidade técnica na execução, profissional extremamente cuidadoso no processo e atento às revisões.",
            "Boa comunicação durante o projeto, entregas consistentes e respeito total ao escopo.",
            "Alguns atrasos ocorreram, mas o resultado final demonstrou domínio técnico e capricho.",
            "Demonstrou iniciativa, organização e ótima postura profissional em todas as etapas.",
            "Trabalho muito bem documentado, seguindo padrões e boas práticas.",
            "Resultado acima do esperado, com otimizações e melhorias além do solicitado.",
            "Profissional comprometido, aberto a feedback e com postura exemplar.",
            "Execução precisa, comunicação clara e facilidade para alinhar expectativas.",
            "Superou as expectativas em vários pontos do escopo inicial.",
            "Entregou com qualidade consistente, mantendo organização e clareza."
        ];

        $freelancerComments = [
            "Equipe extremamente organizada, comunicação clara e retorno muito rápido.",
            "Escopo foi bem definido e aprovado sem burocracia, ambiente ótimo de trabalho.",
            "Processos internos eficientes e suporte constante da equipe técnica.",
            "Empresa muito profissional, pagamentos pontuais e alinhamentos objetivos.",
            "Experiência excelente em todas as etapas, desde briefing até entrega final.",
            "Feedbacks construtivos e colaboração ativa, o que facilitou todo o processo.",
            "Ambiente profissional e cordial, facilitando adaptações e revisões.",
            "Organização impecável, documentação clara e respeito ao cronograma."
        ];

        // DEFINIÇÃO DE QUANTAS AVALIAÇÕES POR EMPRESA
        $evaluationMap = [
            "DevTech Solutions"         => 25,
            "Restaurante Sabor Caseiro" => 6,
            "Buffet Prime Festas"       => 5,
            "ServManutenções Pro"       => 4,
            "Agência Criativa PixelUp"  => 7,
        ];

        foreach ($evaluationMap as $companyName => $count) {

            $company = Company::where('display_name', $companyName)->first();
            if (!$company) continue;

            $jobs = JobVacancy::where('company_id', $company->id)->get();
            if ($jobs->isEmpty()) continue;

            // Freelancers compatíveis com o segmento da empresa
            $companySegmentIds = $company->segments->pluck('id');
            $freelancers = Freelancer::whereHas('segments', function ($q) use ($companySegmentIds) {
                $q->whereIn('segments.id', $companySegmentIds);
            })->get();

            if ($freelancers->isEmpty()) continue;

            // Criação das avaliações reais
            for ($i = 0; $i < $count; $i++) {

                $job = $jobs->random();
                $freela = $freelancers->random();

                // Notas realistas (2.5 a 5.0)
                $companyRating    = round(rand(25, 50) / 10, 1);
                $freelancerRating = round(rand(25, 50) / 10, 1);

                // Datas aleatórias entre 2024 e 2025
                $daysAgo = rand(30, 450);

                // APLICAÇÃO FINALIZADA
                Application::create([
                    'job_vacancy_id' => $job->id,
                    'freelancer_id'  => $freela->id,
                    'status'         => 'ended',

                    // Avaliação da empresa → freelancer
                    'company_rating'       => $companyRating,
                    'company_comment'      => $companyComments[array_rand($companyComments)],
                    'company_ratings_json' => [
                        "qualidade"     => $companyRating,
                        "comunicacao"   => round(max(2.5, $companyRating - 0.3), 1),
                        "prazo"         => round(min(5.0, $companyRating + 0.2), 1),
                    ],
                    'evaluated_by_company_at' => now()->subDays($daysAgo),

                    // Avaliação do freelancer → empresa
                    'freelancer_rating'       => $freelancerRating,
                    'freelancer_comment'      => $freelancerComments[array_rand($freelancerComments)],
                    'freelancer_ratings_json' => [
                        "qualidade"         => $freelancerRating,
                        "profissionalismo"  => round(min(5.0, $freelancerRating + 0.2), 1),
                        "entrega"           => round(max(2.5, $freelancerRating - 0.1), 1),
                    ],
                    'evaluated_by_freelancer_at' => now()->subDays(rand(15, $daysAgo)),
                ]);
            }
        }

        $this->command?->info("Avaliações reais criadas com sucesso! DevTech e Lucas Teixeira com histórico completo.");
    }
}
