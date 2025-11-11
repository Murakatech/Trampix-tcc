<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Freelancer;
use App\Models\Preference;
use App\Models\Segment;

class SetUserSegment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage: php artisan user:segment-set {email} {segmentName?}
     */
    protected $signature = 'user:segment-set {email} {segmentName?}';

    /**
     * The console command description.
     */
    protected $description = 'Define ou corrige o segmento do usuário (freelancer) e atualiza as preferências (segments) para refletir o ID do segmento.';

    public function handle(): int
    {
        $email = $this->argument('email');
        $segmentName = $this->argument('segmentName');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuário não encontrado: {$email}");
            return self::FAILURE;
        }

        $freelancer = Freelancer::where('user_id', $user->id)->first();
        if (!$freelancer) {
            $this->error('Este usuário não possui perfil de freelancer.');
            return self::FAILURE;
        }

        // Resolve o segmento alvo
        $segment = null;
        if ($segmentName) {
            $segment = Segment::where('name', $segmentName)->first();
            if (!$segment) {
                $segment = Segment::where('name', 'like', "%{$segmentName}%").first();
            }
        }
        if (!$segment) {
            // fallback para um segmento existente
            $segment = Segment::orderBy('id')->first();
        }
        if (!$segment) {
            $this->error('Nenhum segmento encontrado na base de dados. Execute os seeders primeiro.');
            return self::FAILURE;
        }

        // Atualiza freelancer.segment_id se necessário
        $changed = false;
        if (!$freelancer->segment_id) {
            $freelancer->segment_id = $segment->id;
            $freelancer->save();
            $changed = true;
        }

        // Atualiza preferências para usar IDs de segmento
        $pref = Preference::where('user_id', $user->id)->first();
        if (!$pref) {
            $pref = new Preference();
            $pref->user_id = $user->id;
        }
        $segments = $pref->segments ?: [];
        // Se vierem como strings, substitui por array de ids com o segmento escolhido
        $pref->segments = [$segment->id];
        $pref->save();

        $this->info("Segmento definido para {$email}: {$segment->name} (ID {$segment->id})");
        if ($changed) {
            $this->info('freelancer.segment_id atualizado.');
        }
        $this->info('Preferências atualizadas: segments = ['.$segment->id.']');

        return self::SUCCESS;
    }
}