<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

class GenerateResumoTecnico extends Command
{
    protected $signature = 'generate:resumo-tecnico';
    protected $description = 'Gera o documento técnico ResumoTecnico.docx para o TCC';

    public function handle(): int
    {
        Settings::setZipClass(Settings::PCLZIP);
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16], ['lineHeight' => 1.5]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14], ['lineHeight' => 1.5]);
        $phpWord->addTitleStyle(3, ['bold' => true, 'size' => 12], ['lineHeight' => 1.5]);

        $section = $phpWord->addSection(['orientation' => 'portrait']);

        $section->addTitle('ResumoTecnico', 1);

        $section->addTitle('1. Visão Geral do Projeto', 2);
        $section->addText('Objetivo principal do Trampix', ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Conectar freelancers e empresas por meio de vagas, candidaturas e avaliações, com suporte a perfis distintos, fluxo de contratação e acompanhamento pós-contrato. [Comentário técnico: Descrever missão e escopo do sistema]', [], ['lineHeight' => 1.5]);

        $section->addText('Tecnologias utilizadas (linguagens, frameworks, bibliotecas)', ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Backend: PHP 8.2, Laravel 12, Sanctum; Frontend: Blade + Alpine.js, TailwindCSS, Vite; Utilitários: Axios, Laravel Breeze (dev), PHPUnit. [Comentário técnico: Detalhar versões e justificativas de escolha]', [], ['lineHeight' => 1.5]);

        $section->addText('Arquitetura geral do sistema', ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Arquitetura MVC com Controllers orquestrando rotas, Views Blade com componentes reutilizáveis, Middleware de autorização por papel (admin, company, freelancer) e serviços de perfil, vagas e candidaturas. [Comentário técnico: Incluir diagrama de camadas e fluxo de dados]', [], ['lineHeight' => 1.5]);

        $section->addText('Público-alvo e contexto de uso', ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Freelancers e empresas em busca de matching eficiente para vagas e projetos, com acompanhamento de aplicações e avaliações. [Comentário técnico: Contextualizar mercado e necessidades]', [], ['lineHeight' => 1.5]);

        $section->addTitle('2. Listagem de Telas e Componentes', 2);

        $this->addScreen($section, 'resources/views/welcome.blade.php', 'Landing pública', 'Página inicial com apresentação do sistema', 'Figura 1: Landing page pública', 'http://localhost:8000/');
        $this->addScreen($section, 'resources/views/auth/login.blade.php', 'Autenticação de usuários', 'Coleta credenciais e autentica via sessões Laravel', 'Figura 2: Interface de login', 'http://localhost:8000/login');
        $this->addScreen($section, 'resources/views/dashboard.blade.php', 'Dashboard geral', 'Entrada pós-login com seções por perfil', 'Figura 3: Dashboard do usuário', 'http://localhost:8000/dashboard');
        $this->addScreen($section, 'resources/views/freelancer/dashboard.blade.php', 'Dashboard freelancer', 'Resumo de atualizações e ações do freelancer', 'Figura 4: Dashboard freelancer', 'http://localhost:8000/freelancer/dashboard');
        $this->addScreen($section, 'resources/views/company/dashboard.blade.php', 'Dashboard empresa', 'Gestão de vagas e aplicações da empresa', 'Figura 5: Dashboard empresa', 'http://localhost:8000/company/dashboard');
        $this->addScreen($section, 'resources/views/vagas/index.blade.php', 'Lista de vagas', 'Listagem pública de vagas com filtros', 'Figura 6: Lista de vagas', 'http://localhost:8000/vagas');
        $this->addScreen($section, 'resources/views/vagas/show.blade.php', 'Detalhe de vaga', 'Página pública de detalhes da vaga', 'Figura 7: Detalhes da vaga', 'http://localhost:8000/vagas/{id}');
        $this->addScreen($section, 'resources/views/applications/index.blade.php', 'Minhas candidaturas', 'Acompanhamento de candidaturas do usuário', 'Figura 8: Minhas candidaturas', 'http://localhost:8000/my-applications');
        $this->addScreen($section, 'resources/views/evaluations/form.blade.php', 'Avaliação pós-contrato', 'Formulário para avaliação de parceiros após contrato', 'Figura 9: Avaliação', 'http://localhost:8000/applications/{application}/evaluate');
        $this->addScreen($section, 'resources/views/connect/index.blade.php', 'Conectar', 'Interface de cards para decidir sobre recomendações de match', 'Figura 10: Conectar', 'http://localhost:8000/connect');

        $section->addText('Componentes principais (Blade):', ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('components/sidebar.blade.php — Navegação lateral reutilizável. [Encontrar em resources/views/components/sidebar.blade.php]', [], ['lineHeight' => 1.5]);
        $section->addText('components/filter-panel.blade.php — Painel de filtros. [Encontrar em resources/views/components/filter-panel.blade.php]', [], ['lineHeight' => 1.5]);
        $section->addText('components/modal.blade.php — Componente modal genérico. [Encontrar em resources/views/components/modal.blade.php]', [], ['lineHeight' => 1.5]);

        $section->addTitle('3. Fluxos Principais', 2);
        $section->addText('Diagrama de sequência (texto descritivo):', ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Fluxo de cadastro: Usuário → /register → Controller RegisteredUser → Cria usuário → Redireciona para seleção de perfil. [Comentário técnico: Inserir diagrama UML na versão final]', [], ['lineHeight' => 1.5]);
        $section->addText('Fluxo de login: Usuário → /login → AuthenticatedSessionController → Valida credenciais → /dashboard. [Comentário técnico: Indicar middleware e políticas]', [], ['lineHeight' => 1.5]);
        $section->addText('Fluxo de criação de vaga (empresa): Empresa → /job-vacancies/create → JobVacancyController@create/store → Vaga listada → Aplicações. [Comentário técnico: Regras de autorização can:isCompany]', [], ['lineHeight' => 1.5]);
        $section->addText('Fluxo de candidatura (freelancer): Freelancer → /vagas/{id} → applications.store → Application criada → Acompanhamento em /my-applications. [Comentário técnico: Validações de elegibilidade]', [], ['lineHeight' => 1.5]);
        $section->addText('Interação entre componentes: Controllers coordenam Models e Views; Blade componentes agrupam UI; Alpine.js adiciona interatividade local; APIs públicas para sugestão e categorias. [Comentário técnico: Destacar responsabilidades]', [], ['lineHeight' => 1.5]);
        $section->addText('Pontos críticos e decisões: Papéis de usuário, segurança via middleware e CSRF, estado de vagas e candidaturas, desacoplamento de Connect. [Comentário técnico: Justificar decisões]', [], ['lineHeight' => 1.5]);

        $section->addTitle('4. Dados Técnicos Relevantes', 2);
        $section->addText('Requisitos não funcionais: Desempenho razoável via cache e consultas otimizadas; Segurança com CSRF, autenticação, autorização por papéis; Manutenibilidade com padrões MVC e componentes reutilizáveis. [Comentário técnico: Medidas de teste e métricas]', [], ['lineHeight' => 1.5]);
        $section->addText('Padrões de código: PSR-4 autoload, convenções Laravel, organização por módulos (perfil, vagas, aplicações), estilo de views com Tailwind e Blade components. [Comentário técnico: Referenciar guia interno]', [], ['lineHeight' => 1.5]);
        $section->addText('Estrutura de pastas: app/ (Models, Http/Controllers), resources/views (Blade), routes/ (web.php, auth.php), public/, database/, config/. [Comentário técnico: Inserir figura de estrutura]', [], ['lineHeight' => 1.5]);
        $section->addText('Dependências externas: Laravel Framework, Sanctum, TailwindCSS, Alpine.js, Axios, Vite, PHPUnit. [Comentário técnico: Atualizar versões e changelog]', [], ['lineHeight' => 1.5]);

        $section->addTitle('5. Considerações para o TCC', 2);
        $section->addText('Sugestões de tópicos: Avaliação de matching; Experiência de usuário; Desempenho em listagens; Segurança e privacidade; Arquitetura de componentes UI. [Comentário técnico: Planejar metodologia]', [], ['lineHeight' => 1.5]);
        $section->addText('Pontos técnicos de destaque: Papéis e autorização, fluxo de candidaturas, avaliações, desacoplamento do módulo Connect, APIs auxiliares de busca. [Comentário técnico: Evidenciar decisões]', [], ['lineHeight' => 1.5]);
        $section->addText('Possíveis melhorias: Reativar Connect com backend dedicado; Integração em tempo real; Indexação de busca; Testes end-to-end; Observabilidade. [Comentário técnico: Priorização futura]', [], ['lineHeight' => 1.5]);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $outputDir = base_path('docs');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        $writer->save($outputDir . DIRECTORY_SEPARATOR . 'ResumoTecnico.docx');

        $this->info('Documento gerado em docs/ResumoTecnico.docx');
        return self::SUCCESS;
    }

    private function addScreen($section, string $file, string $funcao, string $descricao, string $legenda, string $url): void
    {
        $section->addText($file, ['bold' => true], ['lineHeight' => 1.5]);
        $section->addText('Função: ' . $funcao, [], ['lineHeight' => 1.5]);
        $section->addText('Descrição: ' . $descricao, [], ['lineHeight' => 1.5]);
        $section->addText('Legenda TCC: ' . $legenda, [], ['lineHeight' => 1.5]);
        $section->addText('URL/Localização: ' . $url, [], ['lineHeight' => 1.5]);
        $section->addText('[Comentário técnico: Capturar print desta tela/componente]', ['italic' => true], ['lineHeight' => 1.5]);
        $section->addTextBreak(1);
    }
}
