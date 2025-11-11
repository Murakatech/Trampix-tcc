<?php

namespace App\Services;

use App\Models\User;

class RecommendationService
{
    /**
     * Gera o batch diário de recomendações para um usuário.
     */
    public function generateDailyBatchFor(User $user): void
    {
        // TODO: implementar geração baseada em Preference, perfis e vagas
    }

    /**
     * Obtém o próximo card (recommendation) para o usuário.
     */
    public function nextCardFor(User $user): ?array
    {
        // TODO: implementar busca na tabela recommendations e retornar payload
        return null;
    }

    /**
     * Persiste a decisão do usuário sobre um card.
     */
    public function decide(User $user, array $payload): void
    {
        // TODO: persistir em recommendations.status + decided_at e criar match quando aplicável
    }
}