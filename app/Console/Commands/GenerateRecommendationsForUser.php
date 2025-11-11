<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Freelancer;
use App\Services\RecommendationService;

class GenerateRecommendationsForUser extends Command
{
    protected $signature = 'connect:generate-for {email}';
    protected $description = 'Gera recomendações (batch diário) imediatamente para o usuário informado.';

    public function handle(RecommendationService $service): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuário não encontrado: {$email}");
            return self::FAILURE;
        }
        $freelancer = Freelancer::where('user_id', $user->id)->first();
        if ($freelancer) {
            $count = $service->generateDailyBatchFor($user);
            $this->info("Recomendações geradas para freelancer #{$freelancer->id}: {$count} itens.");
            return self::SUCCESS;
        }
        $this->error('Usuário não é freelancer. (Suporte para empresas pode ser adicionado)');
        return self::FAILURE;
    }
}