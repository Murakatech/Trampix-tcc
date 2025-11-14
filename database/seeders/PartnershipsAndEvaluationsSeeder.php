<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Database\Seeder;

class PartnershipsAndEvaluationsSeeder extends Seeder
{
    public function run(): void
    {
        $endedByFreelancer = [];
        $endedByCompany = [];

        $freelancers = Freelancer::query()->orderBy('id')->get();
        foreach ($freelancers as $freelancer) {
            $app = Application::where('freelancer_id', $freelancer->id)->first();
            if ($app) {
                $companyScore = 4.0 + (($freelancer->id % 3) * 0.3);
                $freelancerScore = 4.0 + (($app->job_vacancy_id % 3) * 0.3);
                $companyRatings = [
                    'qualidade' => $companyScore,
                    'comunicacao' => 4.0,
                    'prazo' => 4.5,
                ];
                $freelancerRatings = [
                    'qualidade' => $freelancerScore,
                    'profissionalismo' => 4.5,
                    'entrega' => 4.2,
                ];

                $app->update([
                    'status' => 'ended',
                    'company_rating' => $companyScore,
                    'company_comment' => 'Projeto concluído com qualidade e alinhamento.',
                    'company_ratings_json' => $companyRatings,
                    'evaluated_by_company_at' => now(),
                    'freelancer_rating' => $freelancerScore,
                    'freelancer_comment' => 'Cliente colaborativo e objetivo claro.',
                    'freelancer_ratings_json' => $freelancerRatings,
                    'evaluated_by_freelancer_at' => now(),
                ]);

                $endedByFreelancer[$freelancer->id] = $app->id;
                $endedByCompany[$app->jobVacancy->company_id] = true;
            }
        }

        $companies = Company::query()->orderBy('id')->get();
        foreach ($companies as $company) {
            $hasEnded = Application::whereHas('jobVacancy', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->where('status', 'ended')->exists();

            if (! $hasEnded) {
                $pending = Application::whereHas('jobVacancy', function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                })->first();

                if ($pending) {
                    $companyRatings = [
                        'qualidade' => 4.3,
                        'comunicacao' => 4.1,
                        'prazo' => 4.2,
                    ];
                    $freelancerRatings = [
                        'qualidade' => 4.4,
                        'profissionalismo' => 4.3,
                        'entrega' => 4.0,
                    ];

                    $pending->update([
                        'status' => 'ended',
                        'company_rating' => 4.3,
                        'company_comment' => 'Entrega consistente dentro do escopo.',
                        'company_ratings_json' => $companyRatings,
                        'evaluated_by_company_at' => now(),
                        'freelancer_rating' => 4.3,
                        'freelancer_comment' => 'Boa comunicação e clareza de requisitos.',
                        'freelancer_ratings_json' => $freelancerRatings,
                        'evaluated_by_freelancer_at' => now(),
                    ]);
                }
            }
        }

        $lines = [];
        $lines[] = 'Resumo dos Seeders: Empresas, Vagas, Freelancers, Aplicações e Avaliações';
        $lines[] = 'Credenciais de teste';
        $lines[] = 'Senha padrão: Trampix@123';
        $admins = User::where('role', 'admin')->get();
        if ($admins->count() > 0) {
            $lines[] = 'Logins de Admin:';
            foreach ($admins as $admin) {
                $lines[] = '- '.$admin->name.': '.$admin->email.' / Trampix@123';
            }
        }
        $lines[] = 'Logins de Empresas:';
        foreach ($companies as $company) {
            if ($company->user) {
                $lines[] = '- '.$company->display_name.': '.$company->user->email.' / Trampix@123';
            }
        }
        $lines[] = 'Logins de Freelancers:';
        foreach ($freelancers as $freelancer) {
            if ($freelancer->user) {
                $lines[] = '- '.$freelancer->display_name.': '.$freelancer->user->email.' / Trampix@123';
            }
        }

        foreach ($freelancers as $freelancer) {
            $apps = Application::with(['jobVacancy.company'])
                ->where('freelancer_id', $freelancer->id)
                ->get();
            $companiesApplied = $apps->pluck('jobVacancy.company.display_name')->unique()->values()->all();
            $endedCount = $apps->where('status', 'ended')->count();
            $lines[] = '- Freela '.$freelancer->display_name.' do segmento {'.($freelancer->segment ? $freelancer->segment->name : 'N/A').'} aplicou para: '.implode(', ', $companiesApplied).'; finalizados: '.$endedCount;
        }

        foreach ($companies as $company) {
            $jobs = JobVacancy::where('company_id', $company->id)->get();
            $applications = Application::whereIn('job_vacancy_id', $jobs->pluck('id'))->get();
            $ended = $applications->where('status', 'ended');
            $lines[] = '- Empresa '.$company->display_name.' do segmento {'.($company->segment ? $company->segment->name : 'N/A').'} tem '.$ended->count().' trabalhos finalizados; vagas: '.$jobs->pluck('title')->implode(', ');
        }

        $content = implode(PHP_EOL, $lines).PHP_EOL;
        @file_put_contents(base_path('ResumãoTopSeeders'), $content);
    }
}
