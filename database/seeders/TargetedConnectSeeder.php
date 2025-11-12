<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Segment;
use App\Models\Category;
use App\Models\JobVacancy;
use App\Models\Freelancer;

class TargetedConnectSeeder extends Seeder
{
    /**
     * Cria uma empresa com uma única vaga em "Serviços Gerais" e
     * 10 freelancers no mesmo segmento (Serviços e Operações),
     * para aparecerem como recomendações no módulo Conectar.
     */
    public function run(): void
    {
        // Garantir que o segmento e categoria existam
        $segmentName = 'Serviços e Operações';
        $categoryName = 'Serviços Gerais';
        $segment = Segment::firstOrCreate(['name' => $segmentName]);
        $category = Category::updateOrCreate(
            ['slug' => Str::slug($categoryName)],
            [
                'name' => $categoryName,
                'segment_id' => $segment->id,
            ]
        );

        // Empresa e credenciais
        $companyEmail = 'servops@trampix.com';
        $companyPasswordPlain = 'ServOps@123';

        $companyUser = User::updateOrCreate(
            ['email' => $companyEmail],
            [
                'name' => 'ServOps',
                'role' => 'user',
                'password' => Hash::make($companyPasswordPlain),
                'email_verified_at' => now(),
            ]
        );

        $company = $companyUser->createProfile('company', [
            'display_name' => 'ServOps Ltda',
            'name' => 'Serviços Operacionais Ltda',
            'cnpj' => '00.000.000/0001-00',
            'sector' => 'Serviços',
            'location' => 'São Paulo/SP',
            'description' => 'Empresa focada em serviços gerais e operações.',
            'website' => 'https://servops.local',
            'email' => 'contato@servops.local',
            'phone' => '11900000000',
            'company_size' => 'Pequena',
            'employees_count' => 10,
            'founded_year' => 2020,
            'segment_id' => $segment->id,
        ]);

        // Única vaga ativa nessa empresa
        $job = JobVacancy::create([
            'company_id' => $company->id,
            'title' => 'Vaga SG-01 - Serviços Gerais',
            'description' => 'Buscamos profissional para tarefas de manutenção, limpeza e apoio operacional.',
            'requirements' => 'Organização, proatividade e experiência em serviços gerais.',
            'category' => $categoryName,      // compat legado
            'category_id' => $category->id,   // relacionamento novo
            'service_category_id' => null,
            'location_type' => 'Presencial',
            'salary_range' => 'R$ 2.500 - R$ 4.000',
            'status' => 'active',
        ]);

        // 10 freelancers alinhados ao segmento "Serviços e Operações"
        for ($i = 0; $i < 10; $i++) {
            $f = Freelancer::factory()->create([
                'display_name' => 'Profissional SG '.Str::upper(Str::random(4)),
                'bio' => 'Experiência com serviços gerais, limpeza e apoio em operações.',
                'location' => 'São Paulo/SP',
                'hourly_rate' => 45.00,
                'availability' => 'full_time',
                'segment_id' => $segment->id,
                'is_active' => true,
            ]);
            // Garantir vínculo pelo pivot também
            $f->segments()->syncWithoutDetaching([$segment->id]);
        }

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('TargetedConnectSeeder concluído.');
            $this->command->info('Login da empresa: '.$companyEmail.' | Senha: '.$companyPasswordPlain);
            $this->command->info('ID da vaga criada: '.$job->id.' (categoria: '.$categoryName.' / segmento: '.$segmentName.')');
            $this->command->info('Foram criados 10 freelancers ativos em "'.$segmentName.'".');
        }
    }
}