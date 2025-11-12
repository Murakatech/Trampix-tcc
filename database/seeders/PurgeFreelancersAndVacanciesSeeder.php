<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurgeFreelancersAndVacanciesSeeder extends Seeder
{
    public function run(): void
    {
        // Coletar IDs para exclusões relacionais
        $jobIds = DB::table('job_vacancies')->pluck('id');
        $freelancerIds = DB::table('freelancers')->pluck('id');

        // Remover recomendações relacionadas a vagas
        if ($jobIds->count() > 0) {
            DB::table('recommendations')
                ->where('target_type', 'job')
                ->whereIn('target_id', $jobIds)
                ->delete();
        }

        // Remover recomendações relacionadas a freelancers (como alvo e como sujeito)
        if ($freelancerIds->count() > 0) {
            DB::table('recommendations')
                ->where('target_type', 'freelancer')
                ->whereIn('target_id', $freelancerIds)
                ->delete();

            DB::table('recommendations')
                ->where('subject_type', 'freelancer')
                ->whereIn('subject_id', $freelancerIds)
                ->delete();
        }

        // Excluir vagas (cascateia candidaturas, matches, etc.)
        if ($jobIds->count() > 0) {
            DB::table('job_vacancies')->whereIn('id', $jobIds)->delete();
        }

        // Excluir freelancers (cascateia pivôs, candidaturas, matches, etc.)
        if ($freelancerIds->count() > 0) {
            DB::table('freelancers')->whereIn('id', $freelancerIds)->delete();
        }
    }
}