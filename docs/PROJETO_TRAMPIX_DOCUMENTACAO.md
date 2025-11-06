# Documentação Completa do Projeto Trampix

Este documento descreve, de forma abrangente, as ferramentas, arquitetura, lógica de negócio, telas, banco de dados e procedimentos operacionais do projeto Trampix. Foi elaborado para ser atualizado facilmente e servir de referência para novos membros da equipe.

Atualizado em: 2025-11-03

---

## 1. Ferramentas Utilizadas

### 1.1 Tecnologias, Frameworks e Bibliotecas

- Backend
  - PHP (>= 8.2 local; CI utiliza 8.3)
  - Laravel Framework (12.x)
  - Laravel Sanctum (4.x) — autenticação via tokens/API
  - Laravel Tinker (2.x) — utilitário de linha de comando para inspeção
  - Laravel Breeze (2.x) — scaffolding de autenticação (dev)
  - Laravel Sail (1.x) — ambiente Docker (dev)
  - Laravel Pint (1.x) — formatação de código (dev)
  - Nunomaduro Collision (8.x) — melhora mensagens de erro (dev)
  - PHPUnit (11.x) — testes unitários/funcionais

- Frontend
  - Vite (7.x) — bundler e dev server
  - Tailwind CSS (3.x) + @tailwindcss/forms
  - Alpine.js (3.x) — interações leves (sidebar, transições)
  - PostCSS (8.x) + Autoprefixer (10.x)
  - Laravel Vite Plugin (2.x)
  - Axios (1.x) — requisições HTTP

- Infra/DevOps
  - GitHub Actions — CI: rodagem de testes e migrações
  - Laragon — ambiente local (PHP/MySQL, Windows)
  - Composer — gerenciador de dependências PHP
  - Node.js/NPM — gerenciador JS (versão sugerida >= 18)

### 1.2 Versões Específicas (conforme arquivos de manifesto)

- composer.json
  - php: ^8.2
  - laravel/framework: ^12.0
  - laravel/sanctum: ^4.2
  - laravel/tinker: ^2.10.1
  - dev:
    - fakerphp/faker: ^1.23
    - laravel/breeze: ^2.3
    - laravel/pail: ^1.2.2
    - laravel/pint: ^1.24
    - laravel/sail: ^1.41
    - mockery/mockery: ^1.6
    - nunomaduro/collision: ^8.6
    - phpunit/phpunit: ^11.5.3

- package.json (devDependencies)
  - vite: ^7.0.4
  - tailwindcss: ^3.1.0
  - @tailwindcss/vite: ^4.0.0
  - @tailwindcss/forms: ^0.5.2
  - alpinejs: ^3.4.2
  - autoprefixer: ^10.4.2
  - postcss: ^8.4.31
  - axios: ^1.11.0
  - concurrently: ^9.0.1
  - laravel-vite-plugin: ^2.0.0

- CI (.github/workflows/ci.yml)
  - PHP: 8.3 (ubuntu-latest)
  - Banco para testes: SQLite (file-based)

### 1.3 Configurações Relevantes de Ambiente

- Variáveis .env (principais)
  - APP_ENV, APP_DEBUG, APP_URL
  - APP_KEY
  - DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
  - SANCTUM_STATEFUL_DOMAINS (se usar SPA/API)
  - QUEUE_CONNECTION (se utilizar filas)
  - MAIL_* (se e quando configurar e-mails)

- Ambiente local (Laragon)
  - PHP >= 8.2
  - MySQL >= 8.0 (ou MariaDB compatível)
  - Node.js >= 18

---

## 2. Lógica do Sistema

### 2.1 Diagrama de Fluxo de Dados (visão textual)

Usuário (Freelancer/Empresa/Admin)
  → Autenticação (Breeze/Sanctum) → Gate/Policies (AuthServiceProvider)
  → Controllers (MVC) → Models (Eloquent) → Banco de Dados (MySQL)
  → Views (Blade) + Vite/Tailwind/Alpine → Interação no browser

Principais fluxos:
- Freelancers: buscam vagas → aplicam → acompanham status
- Empresas: criam/gerenciam vagas → analisam candidaturas
- Administradores: visão geral (dashboard), criar vagas (sem exigir perfil), gerenciar entidades e candidaturas

### 2.2 Arquitetura do Sistema

- Padrão MVC (Laravel)
  - Controllers: ApplicationController, JobVacancyController, CompanyController, FreelancerController, Auth controllers etc.
  - Models: User, Company, Freelancer, JobVacancy, Application, Skill, ServiceCategory.
  - Views: Blade (layouts, dashboard, admin, vagas, companies, freelancers, applications, auth).

- Autorização / Perfis Dinâmicos
  - AuthServiceProvider define gates: isFreelancer, isCompany, isAdmin.
  - Policies registradas: FreelancerPolicy, CompanyPolicy.
  - Suporte a múltiplos perfis por usuário (ver docs/DYNAMIC_PROFILE_SYSTEM.md).

### 2.3 Principais Algoritmos e Processos de Negócio

- Criação de Vagas (JobVacancyController@create/store)
  - Acesso permitido para Empresa OU Admin.
  - Admin pode opcionalmente especificar company_id; se não informado, o sistema cria/usa um perfil de empresa vinculado ao admin para permitir a criação sem bloqueios.
  - Validações de campos: title, description, requirements, category, contract_type, location_type, salary_range.

- Listagem/Busca de Vagas (JobVacancyController@index)
  - Suporta filtros: categories, contract_type, location_type, search.
  - Cache de página por 10 minutos sem filtros para performance.

- Candidaturas (ApplicationController@store/cancel/adminIndex)
  - store: freelancer aplica a uma vaga (cover_letter opcional, status inicial pending).
  - cancel: apenas o freelancer dono pode cancelar e somente se status for pending.
  - adminIndex: visão geral paginada para Admin, com estatísticas total/pending/accepted/rejected.

- Painéis/Dashboards
  - Dashboard geral (route: dashboard).
  - Dashboard Admin (route: admin.dashboard): ações rápidas (freelancers, empresas, candidaturas, criar vaga).

### 2.4 Regras de Validação e Tratamento de Erros

- Validação via $request->validate nos controllers (ex.: JobVacancyController@store).
- Autorização via Gates/Policies; bloqueio retorna 403 (abort(403)).
- Status e transições: candidaturas começam como pending; operações inválidas retornam mensagens de erro amigáveis.
- Middleware: isAdmin aplicado a rotas administrativas.

---

## 3. Resumo das Telas Implementadas

Observação: wireframes e mockups são descritos textualmente abaixo; o projeto inclui um styleguide (resources/views/styleguide.blade.php) que demonstra componentes.

### 3.1 Telas

- Dashboard Geral (resources/views/dashboard.blade.php)
  - Resumo do perfil ativo e atalhos.
  - Navegação para áreas principais.

- Dashboard Admin (resources/views/admin/dashboard.blade.php)
  - Blocos com ações rápidas:
    - Freelancers: lista e gestão.
    - Empresas: lista e gestão.
    - Candidaturas: visão geral com estatísticas.
    - Criar Vaga: acesso direto ao formulário de criação.

- Vagas
  - Index (resources/views/vagas/index.blade.php): listagem com filtros; paginação; estado visual de cada vaga.
  - Show (resources/views/vagas/show.blade.php): detalhes da vaga; botão de aplicar (freelancer).
  - Create (resources/views/vagas/create.blade.php): formulário de criação; para Admin, suporta associar empresa (via company_id no backend; campo visual pode ser adicionado).
  - Edit (resources/views/vagas/edit.blade.php): edição de vagas da empresa.

- Candidaturas
  - Admin (resources/views/admin/applications.blade.php): lista geral com métricas.
  - Freelancer/Company (resources/views/applications/*): telas de aplicação/gestão (conforme rotas configuradas).

- Perfis
  - Company (resources/views/company/*, resources/views/companies/*): criação/edição/gestão de perfil empresa e suas vagas.
  - Freelancer (resources/views/freelancer/*, resources/views/freelancers/*): criação/edição de perfil, skills, CV.

- Autenticação/Conta
  - resources/views/auth/*: login, registro, reset de senha.

- Sidebar (resources/views/components/sidebar.blade.php)
  - Comportamento expand/collapse via Alpine.js.
  - Renderiza itens conforme gates: Admin vê “Dashboard Admin”, “Freelancers”, “Empresas”, “Candidaturas”, “Criar Vaga”; Empresa vê “Gerenciar Vagas”; Freelancer vê “Buscar Vagas”, “Minhas Candidaturas”.

### 3.2 Fluxo de Navegação entre Telas

- Usuário loga → Dashboard apropriado.
- Admin → Sidebar de admin → acessa /admin (dashboard) → navega para freelancers/empresas/candidaturas → cria vagas.
- Empresa → Dashboard → “Gerenciar Vagas” → CRUD de vagas.
- Freelancer → “Buscar Vagas” → “Detalhe da Vaga” → “Aplicar”.

### 3.3 Estados e Comportamentos de Componentes

- Sidebar
  - expanded: true/false (hover); transições com x-transition.
  - Itens condicionados por gates (isAdmin, isCompany, isFreelancer).

- Formulários
  - Estados de erro/validação renderizados com classes is-invalid e mensagens.
  - Botões com classes utilitárias (btn-trampix-primary/secondary) e ícones FontAwesome.

---

## 4. Banco de Dados

### 4.1 Modelo ER (visão geral)

- users (1) ←→ (1) freelancers (por usuário; perfil freelancer)
- users (1) ←→ (1) companies (por usuário; perfil empresa)
- companies (1) ←→ (N) job_vacancies
- job_vacancies (1) ←→ (N) applications
- freelancers (1) ←→ (N) applications
- skills (N) ←→ (N) freelancers (pivot: freelancer_skill)
- service_categories (N) ←→ (N) freelancers (pivot: freelancer_service_category)
- service_categories (N) ←→ (N) companies (pivot: company_service_category)

### 4.2 Esquema de Tabelas (principais campos)

- users
  - id (bigint, PK)
  - name (string)
  - display_name (string, nullable)
  - email (string, unique)
  - role (string, nullable; valores esperados: freelancer|company|admin)
  - email_verified_at (timestamp, nullable)
  - password (string)
  - remember_token (string)
  - timestamps

- companies
  - id (bigint, PK)
  - user_id (FK → users.id, cascadeOnDelete)
  - display_name (string, nullable)
  - name (string)
  - cnpj (string, nullable)
  - sector (string, nullable)
  - location (string, nullable)
  - description (text, nullable)
  - website (string, nullable)
  - phone (string, nullable)
  - employees_count (integer, nullable)
  - founded_year (year, nullable)
  - is_active (boolean, default true)
  - timestamps

- freelancers
  - id (bigint, PK)
  - user_id (FK → users.id, cascadeOnDelete)
  - display_name (string, nullable)
  - bio (text, nullable)
  - portfolio_url (string, nullable)
  - cv_url (string, nullable)
  - phone (string, nullable)
  - location (string, nullable)
  - hourly_rate (decimal(8,2), nullable)
  - availability (enum: available|busy|unavailable, default available)
  - is_active (boolean, default true)
  - timestamps

- job_vacancies
  - id (bigint, PK)
  - company_id (FK → companies.id, cascadeOnDelete)
  - title (string)
  - description (text)
  - requirements (text, nullable)
  - category (string, nullable)
  - contract_type (enum: PJ|CLT|Estágio|Freelance, nullable)
  - location_type (enum: Remoto|Híbrido|Presencial, nullable)
  - salary_range (string, nullable)
  - status (enum: active|closed, default active)
  - timestamps

- applications
  - id (bigint, PK)
  - job_vacancy_id (FK → job_vacancies.id, cascade)
  - freelancer_id (FK → freelancers.id, cascade)
  - cover_letter (text, nullable)
  - status (string, default pending)
  - timestamps

- skills
  - id (bigint, PK)
  - name (string) [ver migration 2025_09_23_233213_create_skills_table.php]

- freelancer_skill (pivot)
  - freelancer_id (FK → freelancers.id, cascade)
  - skill_id (FK → skills.id, cascade)
  - PK composta (freelancer_id, skill_id)

- service_categories
  - id (bigint, PK)
  - name (string)
  - slug (string, unique)
  - description (text, nullable)
  - icon (string, nullable)
  - is_active (boolean, default true)
  - timestamps

- freelancer_service_category (pivot)
  - id (bigint, PK)
  - freelancer_id (FK → freelancers.id, cascade)
  - service_category_id (FK → service_categories.id, cascade)
  - unique(freelancer_id, service_category_id)
  - timestamps

- company_service_category (pivot)
  - id (bigint, PK)
  - company_id (FK → companies.id, cascade)
  - service_category_id (FK → service_categories.id, cascade)
  - unique(company_id, service_category_id)
  - timestamps

### 4.3 Relacionamentos e Constraints

- Chaves estrangeiras com cascadeOnDelete para entidades dependentes.
- Índices compostos em freelancers e companies para (user_id, is_active) melhorarem consultas por perfis ativos.
- Unicidade em relações N:N (pivots) para evitar duplicidade de categorias.

### 4.4 Procedures e Triggers

- Não há procedures/triggers MySQL implementadas diretamente neste projeto.
- Regras de negócio estão concentradas nos controllers/models.

### 4.5 Políticas de Backup e Recuperação

- Backups recomendados (produção)
  - Banco: mysqldump diário com retenção (ex.: 7, 30, 90 dias).
  - Storage: backup de storage/app/public (uploads) com versionamento.
  - Código: Git (branches, PRs, tags releases).

- Referências no repositório
  - Pasta backup_YYYYMMDD_* com scripts e análises (ex.: backup_20251030_001116/).

- Recuperação
  - Restaurar dump do banco na versão correspondente do schema/migrations.
  - Restaurar arquivos de storage.
  - Rebuild de assets (vite build) se necessário.

---

## 5. Documentação Adicional

### 5.1 Requisitos Não Funcionais

- Segurança
  - Gates/Policies para autorização baseada em perfis.
  - Senhas armazenadas via hashing padrão Laravel.

- Performance
  - Cache de listagem de vagas sem filtros (10 minutos).
  - Índices em colunas críticas (user_id, is_active).

- Manutenibilidade
  - Padrão MVC, organização em controllers/models/views.
  - Testes automatizados via GitHub Actions (SQLite).

- Usabilidade
  - Sidebar responsiva com comportamento hover.
  - Formulários com feedback de validação.

### 5.2 Dependências Externas

- Serviços de e-mail (opcional): configurar MAIL_* no .env conforme provedor.
- Serviços de filas (opcional): Redis/Database para jobs assíncronos.

### 5.3 Configurações de Deploy

- Pré-requisitos
  - PHP >= 8.2, Composer
  - Node.js >= 18, npm
  - MySQL >= 8.0

- Passos
  1. Clonar repositório.
  2. composer install.
  3. npm install.
  4. Copiar .env.example para .env e ajustar variáveis (APP_KEY, DB_* etc.).
  5. php artisan key:generate.
  6. php artisan migrate --force.
  7. npm run build.
  8. Configurar servidor web (Nginx/Apache) apontando para public/.
  9. Configurar filas/cron se necessário.

- Pós-deploy
  - php artisan config:cache, route:cache, view:cache (se aplicável).
  - Monitorar logs (storage/logs) e saúde do app.

### 5.4 Guia de Instalação e Setup (Ambiente de Desenvolvimento)

1. Requisitos: Laragon (Windows) ou PHP+MySQL, Node.js (>=18), Git.
2. Clonar o repositório.
3. composer install.
4. npm install.
5. Copiar .env.example para .env e ajustar DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD.
6. php artisan key:generate.
7. php artisan migrate.
8. php artisan db:seed (opcional: DatabaseSeeder/DevSeeder).
9. npm run dev (Vite) e php artisan serve (servidor Laravel) — ou usar script composer dev para iniciar tudo.
10. Acessar http://127.0.0.1:8000/ ou conforme porta configurada.

---

## Referências de Código

- Rotas: routes/web.php, routes/auth.php
- Controllers: app/Http/Controllers/*
- Models: app/Models/*
- Views: resources/views/*
- Configurações: config/*
- Providers: app/Providers/* (AuthServiceProvider com Gates)
- Migrations/Seeds: database/migrations/*, database/seeders/*
- CI: .github/workflows/ci.yml

---

## Manutenção do Documento

- Atualize versões quando modificar composer.json ou package.json.
- Registre novas rotas/telas adicionadas.
- Atualize o ER sempre que houver novas migrations.