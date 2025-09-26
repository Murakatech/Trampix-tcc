<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Application;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@trampix.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Empresa
        $companyUser = User::updateOrCreate(
            ['email' => 'empresa@trampix.com'],
            [
                'name' => 'TechCorp Solutions',
                'password' => Hash::make('123456'),
                'role' => 'company',
                'email_verified_at' => now(),
            ]
        );

        $company = Company::updateOrCreate(
            ['user_id' => $companyUser->id],
            [
                'name' => 'TechCorp Solutions LTDA',
                'cnpj' => '12.345.678/0001-99',
                'sector' => 'Tecnologia',
                'location' => 'São Paulo/SP',
                'description' => 'Empresa de tecnologia especializada em desenvolvimento de software.',
                'website' => 'https://techcorp.com.br',
                'phone' => '(11) 99999-9999',
                'employees_count' => 50,
                'founded_year' => 2018,
                'is_active' => true,
            ]
        );

        // 3. Freelancer
        $freelancerUser = User::updateOrCreate(
            ['email' => 'freelancer@trampix.com'],
            [
                'name' => 'João Silva',
                'password' => Hash::make('123456'),
                'role' => 'freelancer',
                'email_verified_at' => now(),
            ]
        );

        $freelancer = Freelancer::updateOrCreate(
            ['user_id' => $freelancerUser->id],
            [
                'bio' => 'Desenvolvedor Full Stack com 3 anos de experiência em Laravel, React e Vue.js. Especialista em APIs REST e desenvolvimento ágil.',
                'portfolio_url' => 'https://joaosilva.dev',
                'cv_url' => null,
                'phone' => '(11) 98888-8888',
                'location' => 'São Paulo/SP',
                'hourly_rate' => 75.00,
                'availability' => 'Disponível para projetos de 20h/semana',
                'is_active' => true,
            ]
        );

        // 4. Cinco vagas da empresa
        $jobsData = [
            [
                'title' => 'Desenvolvedor Backend PHP/Laravel',
                'description' => 'Desenvolvimento e manutenção de APIs REST em Laravel. Experiência com MySQL, Redis e testes automatizados.',
                'category' => 'Desenvolvimento',
                'contract_type' => 'PJ',
                'location_type' => 'Remoto',
                'salary_range' => 'R$ 4.000 - R$ 6.000',
                'requirements' => 'PHP 8+, Laravel 9+, MySQL, Git, conhecimento em testes unitários',
            ],
            [
                'title' => 'Designer UI/UX',
                'description' => 'Criação de interfaces modernas e intuitivas. Desenvolvimento de protótipos e wireframes para aplicações web e mobile.',
                'category' => 'Design',
                'contract_type' => 'CLT',
                'location_type' => 'Híbrido',
                'salary_range' => 'R$ 3.500 - R$ 5.500',
                'requirements' => 'Figma, Adobe XD, Sketch, conhecimento em Design System, portfolio obrigatório',
            ],
            [
                'title' => 'Analista de Dados',
                'description' => 'Análise de dados, criação de dashboards e relatórios. Trabalho com grandes volumes de dados e business intelligence.',
                'category' => 'Dados',
                'contract_type' => 'PJ',
                'location_type' => 'Presencial',
                'salary_range' => 'R$ 5.000 - R$ 8.000',
                'requirements' => 'Python, SQL, Power BI, Excel avançado, estatística básica',
            ],
            [
                'title' => 'Especialista em DevOps',
                'description' => 'Automação de infraestrutura, CI/CD, monitoramento e deploy de aplicações em ambiente cloud.',
                'category' => 'Infraestrutura',
                'contract_type' => 'CLT',
                'location_type' => 'Remoto',
                'salary_range' => 'R$ 7.000 - R$ 12.000',
                'requirements' => 'AWS/Azure, Docker, Kubernetes, Jenkins, Terraform, Linux',
            ],
            [
                'title' => 'Desenvolvedor Frontend React',
                'description' => 'Desenvolvimento de interfaces web responsivas com React.js. Integração com APIs REST e foco em performance.',
                'category' => 'Desenvolvimento',
                'contract_type' => 'PJ',
                'location_type' => 'Híbrido',
                'salary_range' => 'R$ 4.500 - R$ 7.000',
                'requirements' => 'React 18+, TypeScript, Next.js, Tailwind CSS, Git, testes com Jest',
            ],
        ];

        $createdJobs = [];
        foreach ($jobsData as $jobData) {
            $job = JobVacancy::updateOrCreate(
                ['company_id' => $company->id, 'title' => $jobData['title']],
                array_merge($jobData, [
                    'company_id' => $company->id,
                    'status' => 'active',
                    'created_at' => now()->subDays(rand(1, 30)),
                ])
            );
            $createdJobs[] = $job;
        }

        // 5. Candidaturas do freelancer (3 vagas)
        $applicationsData = [
            [
                'job_vacancy_id' => $createdJobs[0]->id, // Backend PHP
                'cover_letter' => 'Tenho 3 anos de experiência com Laravel e PHP. Já desenvolvi diversas APIs REST e tenho conhecimento sólido em testes automatizados.',
                'status' => 'pending',
            ],
            [
                'job_vacancy_id' => $createdJobs[4]->id, // Frontend React
                'cover_letter' => 'Sou especialista em React e TypeScript. Tenho experiência com Next.js e já desenvolvi várias aplicações web responsivas.',
                'status' => 'accepted',
            ],
            [
                'job_vacancy_id' => $createdJobs[1]->id, // Designer UI/UX
                'cover_letter' => 'Embora minha especialidade seja desenvolvimento, tenho conhecimentos em design e já criei algumas interfaces. Gostaria de expandir minha atuação.',
                'status' => 'rejected',
            ],
        ];

        foreach ($applicationsData as $appData) {
            Application::updateOrCreate(
                [
                    'job_vacancy_id' => $appData['job_vacancy_id'],
                    'freelancer_id' => $freelancer->id,
                ],
                array_merge($appData, [
                    'freelancer_id' => $freelancer->id,
                    'created_at' => now()->subDays(rand(1, 15)),
                ])
            );
        }

        // 6. Usuário com múltiplos perfis
        $multiUser = User::updateOrCreate(
            ['email' => 'multi@trampix.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('123456'),
                'role' => 'freelancer', // role padrão
                'email_verified_at' => now(),
            ]
        );

        // Perfil freelancer
        Freelancer::updateOrCreate(
            ['user_id' => $multiUser->id],
            [
                'bio' => 'Consultora em Marketing Digital e Desenvolvedora Frontend. Especialista em estratégias digitais e interfaces modernas.',
                'portfolio_url' => 'https://mariasantos.com',
                'cv_url' => null,
                'phone' => '(11) 97777-7777',
                'location' => 'Rio de Janeiro/RJ',
                'hourly_rate' => 120.00,
                'availability' => 'Disponível para consultoria e projetos',
                'is_active' => true,
            ]
        );

        // Perfil empresa
        Company::updateOrCreate(
            ['user_id' => $multiUser->id],
            [
                'name' => 'Santos Digital Marketing',
                'cnpj' => '98.765.432/0001-11',
                'sector' => 'Marketing Digital',
                'location' => 'Rio de Janeiro/RJ',
                'description' => 'Agência especializada em marketing digital e desenvolvimento web.',
                'website' => 'https://santosdigital.com.br',
                'phone' => '(21) 99999-8888',
                'employees_count' => 8,
                'founded_year' => 2020,
                'is_active' => true,
            ]
        );

        echo "✅ DevSeeder executado com sucesso!\n";
        echo "📧 Admin: admin@trampix.com | Senha: 123456\n";
        echo "🏢 Empresa: empresa@trampix.com | Senha: 123456\n";
        echo "👤 Freelancer: freelancer@trampix.com | Senha: 123456\n";
        echo "🔄 Múltiplos Perfis: multi@trampix.com | Senha: 123456\n";
        echo "📊 Criadas 5 vagas e 3 candidaturas de teste\n";
    }
}
