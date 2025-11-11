<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\RecommendationService;

class GenerateRecommendationsJob
{
    /**
     * Executa a geração diária de recomendações para todos os usuários com perfil ativo.
     */
    public function handle(RecommendationService $service): void
    {
        // Percorre usuários que têm perfil ativo freelancer ou company
        User::query()->whereHas('freelancers', function($q){ $q->where('is_active', true); })
            ->orWhereHas('companies', function($q){ $q->where('is_active', true); })
            ->chunk(200, function($users) use ($service) {
                foreach ($users as $user) {
                    try {
                        $service->generateDailyBatchFor($user);
                    } catch (\Throwable $e) {
                        // Por simplicidade, silencia erros aqui; poderia logar
                        \Log::warning('GenerateRecommendationsJob falhou para user '.$user->id.': '.$e->getMessage());
                    }
                }
            });
    }
}