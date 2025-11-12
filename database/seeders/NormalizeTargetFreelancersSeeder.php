<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Freelancer;
use App\Models\Segment;

class NormalizeTargetFreelancersSeeder extends Seeder
{
    /**
     * Atualiza os 10 freelancers criados para o segmento "Serviços e Operações"
     * definindo e-mail e senha determinísticos para facilitar login de testes.
     */
    public function run(): void
    {
        $segment = Segment::firstOrCreate(['name' => 'Serviços e Operações']);

        $targets = Freelancer::query()
            ->where('segment_id', $segment->id)
            ->where('display_name', 'like', 'Profissional SG %')
            ->orderBy('id')
            ->take(10)
            ->get();

        if ($targets->isEmpty()) {
            if (property_exists($this, 'command') && $this->command) {
                $this->command->warn('Nenhum freelancer alvo encontrado para normalização. Execute TargetedConnectSeeder antes.');
            }
            return;
        }

        $logins = [];
        $i = 1;
        foreach ($targets as $f) {
            $email = 'sg'.str_pad((string)$i, 2, '0', STR_PAD_LEFT).'@trampix.com';
            $user = $f->user;
            if (!$user) { $i++; continue; }
            $user->email = $email;
            $user->password = Hash::make('Freelancer@123');
            $user->email_verified_at = now();
            $user->save();

            $logins[] = [
                'display_name' => $f->display_name,
                'email' => $email,
                'password' => 'Freelancer@123',
            ];
            $i++;
        }

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('NormalizeTargetFreelancersSeeder concluído. Credenciais atualizadas:');
            foreach ($logins as $l) {
                $this->command->info('- '.$l['display_name'].': '.$l['email'].' | Senha: '.$l['password']);
            }
        }
    }
}