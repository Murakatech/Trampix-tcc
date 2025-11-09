<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Application;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('display_name', 'MurakaTech')->first();
        $freelancer = Freelancer::whereHas('user', function ($q) {
            $q->where('email', 'muraka@trampix.com');
        })->first();

        if (!$company || !$freelancer) {
            if (property_exists($this, 'command') && $this->command) {
                $this->command->error('FeedbackSeeder: Missing company or freelancer. Ensure AccountsSeeder and VacanciesSeeder ran.');
            }
            return;
        }

        $vacancies = JobVacancy::where('company_id', $company->id)->orderBy('id')->get();
        if ($vacancies->count() < 21) {
            if (property_exists($this, 'command') && $this->command) {
                $this->command->warn('FeedbackSeeder: Expected 30 vacancies; found ' . $vacancies->count() . '. Proceeding with available.');
            }
        }

        // Find vacancies that already have pending applications (from VacanciesSeeder) to avoid overlap
        $alreadyAppliedVacancyIds = Application::where('freelancer_id', $freelancer->id)
            ->pluck('job_vacancy_id')
            ->all();

        $availableVacancies = $vacancies->whereNotIn('id', $alreadyAppliedVacancyIds)->values();
        $idx = 0;

        // 2 rejected
        for ($i = 0; $i < 2; $i++) {
            $v = $availableVacancies[$idx++] ?? null;
            if (!$v) break;
            $v->applications()->create([
                'freelancer_id' => $freelancer->id,
                'cover_letter' => 'Apresentação enviada, porém sem retorno positivo.',
                'status' => 'rejected',
                'rejected_acknowledged' => true,
            ]);
        }

        // 2 accepted
        for ($i = 0; $i < 2; $i++) {
            $v = $availableVacancies[$idx++] ?? null;
            if (!$v) break;
            $v->applications()->create([
                'freelancer_id' => $freelancer->id,
                'cover_letter' => 'Aplicação aceita. Aguardando início.',
                'status' => 'accepted',
            ]);
        }

        // 2 ended (finalizadas) without evaluations
        for ($i = 0; $i < 2; $i++) {
            $v = $availableVacancies[$idx++] ?? null;
            if (!$v) break;
            $v->applications()->create([
                'freelancer_id' => $freelancer->id,
                'cover_letter' => 'Projeto finalizado. Avaliações pendentes.',
                'status' => 'ended',
            ]);
        }

        // 10 ended with evaluations (both sides)
        for ($i = 0; $i < 10; $i++) {
            $v = $availableVacancies[$idx++] ?? null;
            if (!$v) break;

            // Gerar 10 notas (1-5) para corresponder às 10 perguntas em evaluations.show/form
            $companyRatings = [];
            $freelancerRatings = [];
            for ($q = 0; $q < 10; $q++) {
                $companyRatings[$q] = rand(3, 5);
                $freelancerRatings[$q] = rand(3, 5);
            }

            $companyAvg = array_sum($companyRatings) / count($companyRatings);
            $freelancerAvg = array_sum($freelancerRatings) / count($freelancerRatings);

            $v->applications()->create([
                'freelancer_id' => $freelancer->id,
                'cover_letter' => 'Projeto concluído com sucesso. Avaliações registradas.',
                'status' => 'ended',
                'company_rating' => (int)round($companyAvg),
                'company_comment' => 'Profissional excelente, comunicação clara e entrega dentro do prazo.',
                'company_ratings_json' => $companyRatings,
                'company_rating_avg' => round($companyAvg, 1),
                'evaluated_by_company_at' => now(),
                'freelancer_rating' => (int)round($freelancerAvg),
                'freelancer_comment' => 'Empresa organizada, pagamentos em dia e ótimo alinhamento de expectativas.',
                'freelancer_ratings_json' => $freelancerRatings,
                'freelancer_rating_avg' => round($freelancerAvg, 1),
                'evaluated_by_freelancer_at' => now(),
            ]);
        }

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('Feedback seeded: 2 rejected, 2 accepted, 2 ended (no eval), 10 ended (with eval).');
        }
    }
}