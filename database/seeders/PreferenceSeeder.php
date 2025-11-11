<?php

namespace Database\Seeders;

use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Seeder;

class PreferenceSeeder extends Seeder
{
    public function run(): void
    {
        // Freelancer preference
        $freelancerUser = User::whereHas('freelancer')->first();
        if ($freelancerUser) {
            Preference::updateOrCreate([
                'user_id' => $freelancerUser->id,
                'role' => 'freelancer',
            ], [
                'desired_roles' => ['Desenvolvedor Laravel','Full-Stack'],
                'segments' => ['Tecnologia','Software'],
                'skills' => ['Laravel','PHP','MySQL','Tailwind'],
                'seniority_min' => 2,
                'seniority_max' => 5,
                'remote_ok' => true,
                'salary_min' => 6000,
                'salary_max' => 12000,
                'location' => 'São Paulo, SP',
                'radius_km' => 0,
            ]);
        }

        // Company preference
        $companyUser = User::whereHas('company')->first();
        if ($companyUser) {
            Preference::updateOrCreate([
                'user_id' => $companyUser->id,
                'role' => 'company',
            ], [
                'desired_roles' => ['Freelancer Back-end','Freelancer Full-Stack'],
                'segments' => ['Tecnologia','Consultoria'],
                'skills' => ['Laravel','Docker','CI/CD'],
                'seniority_min' => 3,
                'seniority_max' => 7,
                'remote_ok' => true,
                'salary_min' => null,
                'salary_max' => null,
                'location' => 'Remoto',
                'radius_km' => null,
            ]);
        }

        // Fallback: se não houver usuários, cria preferências genéricas para user 1 (se existir)
        $user1 = User::find(1);
        if ($user1) {
            Preference::firstOrCreate([
                'user_id' => $user1->id,
                'role' => 'freelancer',
            ], [
                'desired_roles' => ['Engenheiro de Software'],
                'segments' => ['Tecnologia'],
                'skills' => ['PHP','Laravel'],
                'seniority_min' => 1,
                'seniority_max' => 10,
                'remote_ok' => true,
                'salary_min' => null,
                'salary_max' => null,
                'location' => null,
                'radius_km' => null,
            ]);
        }
    }
}