<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Freelancer;
use App\Models\Company;
use App\Models\Segment;
use App\Models\JobVacancy;

class DevMatchmakingSeeder extends Seeder
{
    public function run(): void
    {
        $segment = Segment::firstOrCreate(
            ['name' => 'Tecnologia'],
            ['active' => true]
        );

        $companyUser = User::updateOrCreate(
            ['email' => 'empresa@dev.test'],
            [
                'name' => 'Dev Company',
                'password' => Hash::make('Trampix@123'),
                'email_verified_at' => now(),
            ]
        );

        $company = Company::updateOrCreate(
            ['user_id' => $companyUser->id],
            [
                'display_name' => 'Dev Company',
                'name' => 'Dev Company',
                'sector' => 'Tecnologia',
                'location' => 'São Paulo/SP',
                'description' => 'Empresa de teste para matchmaking',
                'segment_id' => $segment->id,
                'is_active' => true,
            ]
        );
        $company->segments()->sync([$segment->id]);

        $freelancerUser = User::updateOrCreate(
            ['email' => 'freela@dev.test'],
            [
                'name' => 'Dev Freela',
                'password' => Hash::make('Trampix@123'),
                'email_verified_at' => now(),
            ]
        );

        $freelancer = Freelancer::updateOrCreate(
            ['user_id' => $freelancerUser->id],
            [
                'display_name' => 'Dev Freela',
                'bio' => 'Freelancer de front-end para projetos web.',
                'location' => 'Remoto',
                'hourly_rate' => 120.00,
                'availability' => 'project_based',
                'whatsapp' => '5511999999999',
                'segment_id' => $segment->id,
                'is_active' => true,
            ]
        );
        $freelancer->segments()->sync([$segment->id]);

        JobVacancy::updateOrCreate(
            [
                'company_id' => $company->id,
                'title' => 'Projeto Front-end React',
            ],
            [
                'description' => 'Implementação de landing page e componentes UI.',
                'status' => 'active',
                'location_type' => 'Remoto',
                'salary_range' => 'R$ 5.000 - R$ 7.000',
                'category' => 'Tecnologia',
            ]
        );
    }
}
