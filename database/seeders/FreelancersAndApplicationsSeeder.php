<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FreelancersAndApplicationsSeeder extends Seeder
{
    public function run(): void
    {
        $segments = Segment::with('categories')->get();

        if ($segments->isEmpty()) {
            $this->command?->error("Rodar antes o seeder de Segmentos e Categorias!");
            return;
        }

        // -------- ENDEREÇOS 100% região de Ribeirão Preto --------
        $locations = [
            "Ribeirão Preto/SP",
            "Sertãozinho/SP",
            "Jardinópolis/SP",
            "Cravinhos/SP",
            "Serrana/SP",
            "Bonfim Paulista/SP",
            "Dumont/SP",
            "Brodowski/SP",
            "Guatapará/SP",
            "Pontal/SP",
            "Barrinha/SP",
        ];

        // -------- NOMES BRASILEIROS POR SEGMENTO --------
        $nomeBase = [
            "Gastronomia & Eventos" => [
                "Ana Paula Ferreira",
                "João Victor Carvalho"
            ],
            "Serviços Gerais & Operacionais" => [
                "Carlos Henrique Souza",
                "Marcos Almeida"
            ],
            "Comércio & Atendimento" => [
                "Fernanda Ribeiro",
                "Lucas Teixeira"
            ],
            "Criatividade, Mídia & Conteúdo" => [
                "Beatriz Santos",
                "Felipe Rodrigues"
            ],
            "Tecnologia & Desenvolvimento" => [
                "Rafael Lima",
                "Thiago Martins"
            ],
            "Saúde, Beleza & Bem-estar" => [
                "Camila Nogueira",
                "Juliana Rocha"
            ],
            "Educação & Especialistas" => [
                "Patrícia Oliveira",
                "Gustavo Mendes"
            ],
        ];

        $freelancers = [];

        // -------- CRIAÇÃO DOS FREELANCERS --------
        foreach ($segments as $segment) {

            $nomes = $nomeBase[$segment->name] ?? [];

            foreach ($nomes as $nome) {

                $slug = Str::slug($nome);
                $email = $slug . '@seed.trampix';

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name'              => $nome,
                        'display_name'      => $nome,
                        'role'              => 'freelancer',
                        'password'          => Hash::make('Trampix@123'),
                        'email_verified_at' => now(),
                    ]
                );

                // BIO REALISTA
                $bio = "{$nome} atua profissionalmente no segmento de {$segment->name}, com sólida experiência prática 
e participação em diversos projetos reais. Possui histórico consistente de entregas com qualidade, boa comunicação 
e foco total em resultados.";

                // PERFIL COMPLETO (SEM FOTO)
                $freela = Freelancer::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'display_name'     => $nome,
                        'bio'              => $bio,
                        'portfolio_url'    => "https://portfolio.trampix.com/{$slug}",
                        'linkedin_url'     => "https://linkedin.com/in/{$slug}",
                        'cv_url'           => "https://files.trampix.com/cv/{$slug}.pdf",
                        'whatsapp'         => '55119'.rand(900000000, 999999999),
                        'location'         => $locations[array_rand($locations)],
                        'hourly_rate'      => rand(60, 150),
                        'availability'     => 'project_based',
                        'is_active'        => true,
                        'segment_id'       => $segment->id,
                    ]
                );

                $freela->segments()->sync([$segment->id]);
                $freelancers[] = $freela;
            }
        }

        // -------- TODAS AS VAGAS --------
        $jobs = JobVacancy::with('category')->get();

        if ($jobs->isEmpty()) {
            $this->command?->error("Rodar antes Empresas + Vagas!");
            return;
        }

        // -------- FREELA → APLICA EM VAGAS DO SEGMENTO --------
        foreach ($freelancers as $freela) {

            $catIds = Category::where('segment_id', $freela->segment_id)->pluck('id');

            $matching = JobVacancy::whereIn('category_id', $catIds)->get();

            if ($matching->isEmpty()) continue;

            $applyIn = $matching->shuffle()->take(rand(4, 6));

            foreach ($applyIn as $job) {

                Application::updateOrCreate(
                    [
                        'job_vacancy_id' => $job->id,
                        'freelancer_id'  => $freela->id,
                    ],
                    [
                        'cover_letter' =>
                            "Olá! Sou {$freela->display_name}, profissional de {$job->category->name}. 
Tenho experiência sólida na área e foco total em qualidade e responsabilidade.",
                        'status' => 'applied',
                    ]
                );
            }
        }

        $this->command?->info("Freelancers e aplicações criados com sucesso (SEM IMAGENS)!");
    }
}
