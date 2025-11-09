<?php

use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyVacancyController;
use App\Http\Controllers\JobVacancyStatusController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FreelancerDashboardController;
use App\Http\Controllers\CompanyDashboardController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// garante que {vaga} só aceite números
Route::pattern('vaga', '[0-9]+');

// Landing Page Principal
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Página pública de documentação sobre Categorias e Áreas de Atuação
Route::view('/docs/categorias-e-areas', 'docs.categories-activity-areas')->name('docs.categories_areas');

// Rotas de Dashboard Pós-Login
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Dashboard específico para freelancers
    Route::middleware(['can:isFreelancer'])->group(function () {
        Route::get('/freelancer/dashboard', [FreelancerDashboardController::class, 'index'])->name('freelancer.dashboard');
        Route::get('/freelancer/dashboard/updates', [FreelancerDashboardController::class, 'getUpdates'])->name('freelancer.dashboard.updates');
    });
    
    // Dashboard específico para empresas
    Route::middleware(['can:isCompany'])->group(function () {
        Route::get('/company/dashboard', [CompanyDashboardController::class, 'index'])->name('company.dashboard');
    });
});

// Protegido (auth + empresa) — registre ANTES
Route::middleware(['auth','can:isCompany'])->group(function () {
    Route::resource('vagas', \App\Http\Controllers\JobVacancyController::class)
        ->only(['create','store','edit','update','destroy']);
    
    // Rota adicional para job-vacancies.create (compatibilidade com dashboard)
    Route::get('/job-vacancies/create', [JobVacancyController::class, 'create'])
        ->name('job-vacancies.create');
    
    // Rotas específicas para vagas da empresa
    Route::get('/company/vagas', [CompanyVacancyController::class, 'index'])->name('company.vagas.index');
    Route::get('/company/vagas/{vaga}', [CompanyVacancyController::class, 'show'])->name('company.vagas.show');
    Route::patch('/company/vagas/{vaga}/toggle-status', [CompanyVacancyController::class, 'toggleStatus'])->name('company.vagas.toggle-status');
    
    Route::get('/job-vacancies/{id}/applications', [ApplicationController::class, 'byVacancy'])
        ->name('applications.byVacancy');
    
    Route::get('/applications/manage', [ApplicationController::class, 'manage'])
        ->name('applications.manage');
    
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])
        ->name('applications.updateStatus');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Styleguide (apenas para desenvolvimento)
Route::get('/styleguide', fn () => view('styleguide'))
    ->middleware(['auth'])
    ->name('styleguide');

// Teste da Sidebar Minimalista
Route::get('/sidebar-test', fn () => view('sidebar-test'))
    ->name('sidebar-test');

// Sidebar Minimalista (versão mais simples)
Route::get('/sidebar-minimal', fn () => view('sidebar-minimal'))
    ->name('sidebar-minimal');

// Sidebar com Componentes
Route::get('/sidebar-components', fn () => view('sidebar-components'))
    ->name('sidebar-components');

// Página de Demonstração das Sidebars
Route::get('/sidebar-demo', fn () => view('sidebar-demo'))
    ->name('sidebar-demo');

// Público
Route::resource('vagas', JobVacancyController::class)->only(['index','show']);
// API pública para sugestões de busca de vagas
Route::get('/api/vagas/suggest', [JobVacancyController::class, 'suggest'])->name('api.vagas.suggest');
// API pública: categorias por segmento (para filtro dinâmico)
Route::get('/api/segments/{segment}/categories', [JobVacancyController::class, 'categoriesBySegment'])
    ->name('api.segments.categories');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
// Perfil público unificado (visualização)
Route::get('/profiles/{user}', [ProfileController::class, 'show'])->name('profiles.show');

// Perfil / dashboard
Route::middleware('auth')->group(function () {
    // Tela de seleção de perfil e troca
    \App\Http\Controllers\Auth\RoleSelectionController::class; // ensure class reference exists for static analysis
    Route::get('/select-role', [\App\Http\Controllers\Auth\RoleSelectionController::class, 'show'])->name('select-role.show');
    Route::post('/select-role', [\App\Http\Controllers\Auth\RoleSelectionController::class, 'select'])->name('select-role.select');
    Route::post('/switch-role', [\App\Http\Controllers\Auth\RoleSelectionController::class, 'switch'])->name('select-role.switch');
    // Nova rota para configurações de conta
    Route::get('/profile/account', [ProfileController::class, 'account'])->name('profile.account');
    Route::patch('/profile/account', [ProfileController::class, 'updateAccount'])->name('profile.account.update');
    
    // Rota principal do perfil (baseada no active_role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rota para trocar perfil
    Route::post('/profile/switch-role', [ProfileController::class, 'switchRole'])->name('profile.switch-role');
    
    // Rota para obter dados do perfil freelancer em JSON
    Route::get('/profile/freelancer', [ProfileController::class, 'showFreelancerProfile'])->name('profile.freelancer.show');
    
    // Rotas para exclusão de perfis específicos
    Route::delete('/profile/freelancer', [ProfileController::class, 'destroyFreelancerProfile'])->name('profile.freelancer.destroy');
    Route::delete('/profile/company', [ProfileController::class, 'destroyCompanyProfile'])->name('profile.company.destroy');
    
    // Rotas para upload de foto de perfil
    Route::post('/profile/photo/upload', [ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo/delete', [ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');
    
    // API para atualização dinâmica de perfil
    Route::get('/api/profile/check-updates', [ProfilePhotoController::class, 'checkUpdates'])->name('api.profile.check-updates');
    Route::get('/api/profile/data', [ProfilePhotoController::class, 'getProfileData'])->name('api.profile.data');
    
    // Rotas para imagens de perfil (freelancer e empresa)
    Route::patch('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.image.update');
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    
    // Rotas para upload de currículo
    Route::post('/profile/cv/upload', [ProfileController::class, 'uploadCv'])->name('profile.cv.upload');
    Route::delete('/profile/cv/delete', [ProfileController::class, 'deleteCv'])->name('profile.cv.delete');
    
    // Rotas para perfis de freelancer
    Route::get('/freelancers/profile', [FreelancerController::class, 'showOwn'])->name('freelancers.profile');
    Route::resource('freelancers', FreelancerController::class)
        ->only(['show', 'create', 'store', 'update', 'destroy']);
    // Redirect legado para edição de freelancer → /profile
    Route::get('/freelancers/{freelancer}/edit', function () {
        return redirect()->route('profile.edit');
    })->name('freelancers.edit');
    Route::get('/freelancers/{freelancer}/download-cv', [FreelancerController::class, 'downloadCv'])
        ->name('freelancers.download-cv');
    
    // Rotas para perfis de empresa (autenticadas)
    Route::resource('companies', CompanyController::class)
        ->only(['create', 'store', 'update', 'destroy']);
    // Redirect legado para edição de empresa → /profile
    Route::get('/companies/{company}/edit', function () {
        return redirect()->route('profile.edit');
    })->name('companies.edit');
    // Removido: rota legada de vagas por empresa (/companies/{company}/vacancies)
});

Route::middleware(['auth'])->group(function () {
    // Página de Status da Vaga (empresa dona ou freelancer aceito)
    Route::get('/vagas/{vaga}/status', [JobVacancyStatusController::class, 'show'])
        ->name('vagas.status');
    // Avaliações pós-contrato (empresa ou freelancer)
    Route::get('/applications/{application}/evaluate', [EvaluationController::class, 'create'])
        ->name('applications.evaluate.create');
    Route::post('/applications/{application}/evaluate', [EvaluationController::class, 'store'])
        ->name('applications.evaluate.store');
    Route::get('/applications/{application}/evaluation', [EvaluationController::class, 'show'])
        ->name('applications.evaluate.show');
    Route::post('/job-vacancies/{id}/apply', [ApplicationController::class, 'store'])
        ->name('applications.store')
        ->middleware('can:isFreelancer');

    Route::get('/my-applications', [ApplicationController::class, 'index'])
        ->name('applications.index');
    // Trabalhos finalizados (empresa ou freelancer, despacha para a view correta)
    Route::get('/finished-jobs', [ApplicationController::class, 'finishedIndex'])
        ->name('finished.index');
    
    Route::delete('/applications/{application}', [ApplicationController::class, 'cancel'])
        ->name('applications.cancel')
        ->middleware('can:isFreelancer');
    // Reconhecer todas as rejeições (freelancer) para ocultar aviso
    Route::post('/applications/ack-rejections', [ApplicationController::class, 'acknowledgeAllRejections'])
        ->name('applications.ack.all')
        ->middleware('can:isFreelancer');

    // Freelancer se demite de uma parceria ativa
    Route::patch('/applications/{application}/resign', [ApplicationController::class, 'resign'])
        ->name('applications.resign')
        ->middleware('can:isFreelancer');
});

// Rotas administrativas
Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::get('/admin/freelancers', [FreelancerController::class, 'index'])->name('admin.freelancers');
    Route::get('/admin/companies', [CompanyController::class, 'index'])->name('admin.companies');
    Route::get('/admin/applications', [ApplicationController::class, 'adminIndex'])->name('admin.applications');
    // Rota específica para admins criarem vagas
    Route::get('/admin/vagas/create', [JobVacancyController::class, 'create'])
        ->name('admin.vagas.create');
    // Rota POST para admins salvarem vagas
    Route::post('/admin/vagas', [JobVacancyController::class, 'store'])
        ->name('admin.vagas.store');

    // Administração de Categorias
    Route::get('/admin/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])
        ->name('admin.categories.index');
    Route::post('/admin/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])
        ->name('admin.categories.store');
    Route::patch('/admin/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])
        ->name('admin.categories.update');
    // Ativação/Desativação de categorias
    Route::patch('/admin/categories/{category}/deactivate', [\App\Http\Controllers\Admin\CategoryController::class, 'deactivate'])
        ->name('admin.categories.deactivate');
    Route::patch('/admin/categories/{category}/reactivate', [\App\Http\Controllers\Admin\CategoryController::class, 'reactivate'])
        ->name('admin.categories.reactivate');

    // Administração de Segmentos (gerenciados na mesma tela de categorias)
    Route::post('/admin/segments', [\App\Http\Controllers\Admin\SegmentController::class, 'store'])
        ->name('admin.segments.store');
    Route::patch('/admin/segments/{segment}', [\App\Http\Controllers\Admin\SegmentController::class, 'update'])
        ->name('admin.segments.update');
    // Ativação/Desativação de segmentos
    Route::patch('/admin/segments/{segment}/deactivate', [\App\Http\Controllers\Admin\SegmentController::class, 'deactivate'])
        ->name('admin.segments.deactivate');
    Route::patch('/admin/segments/{segment}/reactivate', [\App\Http\Controllers\Admin\SegmentController::class, 'reactivate'])
        ->name('admin.segments.reactivate');
});

require __DIR__.'/auth.php';
