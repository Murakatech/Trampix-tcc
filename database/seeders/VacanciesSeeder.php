<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Category;

class VacanciesSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('display_name', 'MurakaTech')->first();
        $freelancer = Freelancer::whereHas('user', function ($q) {
            $q->where('email', 'muraka@trampix.com');
        })->first();

        if (!$company) {
            if (property_exists($this, 'command') && $this->command) {
                $this->command->error('VacanciesSeeder: MurakaTech company not found. Run AccountsSeeder first.');
            }
            return;
        }
        if (!$freelancer) {
            if (property_exists($this, 'command') && $this->command) {
                $this->command->error('VacanciesSeeder: Muraka freelancer not found. Run AccountsSeeder first.');
            }
            return;
        }

        $categories = Category::inRandomOrder()->get();
        if ($categories->isEmpty()) {
            // Fallback categories if none seeded
            $fallback = collect([
                ['name' => 'Desenvolvimento de Software'],
                ['name' => 'UX/UI Design'],
                ['name' => 'Marketing'],
                ['name' => 'Vendas e Comercial'],
                ['name' => 'Infraestrutura e Redes'],
                ['name' => 'Ciência de Dados'],
                ['name' => 'Publicidade e Propaganda'],
                ['name' => 'E-commerce'],
                ['name' => 'Game Design'],
                ['name' => 'Administração'],
            ]);
            $categories = $fallback;
        }

        $locationTypes = ['Remoto', 'Híbrido', 'Presencial'];

        $vacancies = [];
        for ($i = 1; $i <= 30; $i++) {
            $cat = $categories[$i % $categories->count()];
            $catName = is_array($cat) ? $cat['name'] : $cat->name;
            $catId = is_array($cat) ? null : $cat->id;

            $v = JobVacancy::create([
                'company_id' => $company->id,
                'title' => 'Vaga #' . $i . ' - ' . $catName,
                'description' => 'Procura-se profissional para atuar em ' . $catName . ' com foco em qualidade, prazos e colaboração.',
                'requirements' => 'Experiência prévia, comunicação eficiente, proatividade.',
                'category' => $catName,
                'category_id' => $catId,
                'service_category_id' => null,
                'location_type' => $locationTypes[$i % count($locationTypes)],
                'salary_range' => 'R$ ' . (3000 + $i * 100) . ' - R$ ' . (5000 + $i * 150),
                'status' => 'active',
            ]);
            $vacancies[] = $v;
        }

        // Muraka applies to 5 vacancies (awaiting company response)
        $pendingApplied = [];
        for ($i = 0; $i < 5; $i++) {
            $v = $vacancies[$i];
            $app = $v->applications()->create([
                'freelancer_id' => $freelancer->id,
                'cover_letter' => 'Olá! Tenho interesse na vaga ' . $v->title . '. Meu portfólio: https://github.com/muraka',
                'status' => 'pending',
            ]);
            $pendingApplied[] = $app->id;
        }

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('Vacancies seeded: 30 for MurakaTech. Muraka applied to 5 (pending).');
        }
    }
}