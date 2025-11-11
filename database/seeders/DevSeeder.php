<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Category;
use App\Models\Application;

class DevSeeder extends Seeder
{
    /**
     * Seed de desenvolvimento adicional:
     * - Cria 10 novas empresas, cada uma com pelo menos 1 vaga
     * - Cria 10 novos freelancers
     * - Todos os freelancers aplicam para vagas existentes (incluindo as já semeadas)
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        // Coleta de categorias para vincular às vagas
        $categories = Category::inRandomOrder()->get();
        if ($categories->isEmpty()) {
            // Fallback se ainda não houver categorias semeadas
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

        $contractTypes = ['CLT', 'PJ', 'Freelance'];
        $locationTypes = ['Remoto', 'Híbrido', 'Presencial'];

        // 1) 10 novas empresas com 1-3 vagas cada
        $companies = Company::factory(10)->create();
        $createdVacancies = collect();

        foreach ($companies as $company) {
            $vacCount = rand(1, 3);
            for ($i = 0; $i < $vacCount; $i++) {
                $cat = $categories instanceof \Illuminate\Support\Collection
                    ? $categories->get(rand(0, $categories->count() - 1))
                    : null;

                $catName = is_array($cat) ? ($cat['name'] ?? 'Geral') : ($cat?->name ?? 'Geral');
                $catId = is_array($cat) ? null : ($cat->id ?? null);

                $vac = JobVacancy::create([
                    'company_id' => $company->id,
                    'title' => 'Vaga Dev ' . Str::upper(Str::random(5)) . ' - ' . $catName,
                    'description' => 'Estamos em busca de profissional para atuar em ' . $catName . ', colaborando com nosso time e entregando com qualidade.',
                    'requirements' => 'Experiência na área, boa comunicação, proatividade e trabalho em equipe.',
                    // manter compat com legado: salvar nome da categoria em `category` e id em `category_id`
                    'category' => $catName,
                    'category_id' => $catId,
                    'service_category_id' => null,
                    'contract_type' => $contractTypes[array_rand($contractTypes)],
                    'location_type' => $locationTypes[array_rand($locationTypes)],
                    'salary_range' => 'R$ ' . rand(3000, 9000) . ' - R$ ' . rand(9000, 15000),
                    'status' => 'active',
                ]);
                $createdVacancies->push($vac);
            }
        }

        // 2) 10 novos freelancers
        $freelancers = Freelancer::factory(10)->create();

        // 3) Todos os freelancers aplicam para vagas existentes (incluindo as recém-criadas)
        $allVacancies = JobVacancy::active()->get();
        if ($allVacancies->isEmpty()) {
            if (property_exists($this, 'command') && $this->command) {
                $this->command->warn('Não há vagas ativas para aplicar. Execute os seeders de categorias/contas/vagas antes.');
            }
            return;
        }

        foreach ($freelancers as $freelancer) {
            // Cada freelancer aplica entre 4 e 7 vagas distintas
            $applyCount = rand(4, 7);
            $targets = $allVacancies->shuffle()->take($applyCount);

            foreach ($targets as $vacancy) {
                // Evitar duplicidade caso já exista alguma aplicação
                $already = Application::where('job_vacancy_id', $vacancy->id)
                    ->where('freelancer_id', $freelancer->id)
                    ->exists();
                if ($already) continue;

                Application::create([
                    'job_vacancy_id' => $vacancy->id,
                    'freelancer_id' => $freelancer->id,
                    'cover_letter' => 'Olá! Tenho interesse na vaga ' . $vacancy->title . '. Meu portfólio: ' . $faker->url(),
                    'status' => 'pending',
                ]);
            }
        }

        if (property_exists($this, 'command') && $this->command) {
            $this->command->info('DevSeeder: 10 empresas criadas com vagas e 10 freelancers aplicaram em vagas existentes.');
            $this->command->info('Novas vagas criadas: ' . $createdVacancies->count());
        }
    }
}