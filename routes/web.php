<?php

use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FreelancerDashboardController;
use App\Http\Controllers\CompanyDashboardController;
use Illuminate\Support\Facades\Route;

// garante que {vaga} só aceite números
Route::pattern('vaga', '[0-9]+');

// Landing Page Principal
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rotas de Dashboard Pós-Login
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/freelancer/dashboard', [FreelancerDashboardController::class, 'index'])->name('freelancer.dashboard');
    Route::get('/company/dashboard', [CompanyDashboardController::class, 'index'])->name('company.dashboard');
});

// Protegido (auth + empresa) — registre ANTES
Route::middleware(['auth','can:isCompany'])->group(function () {
    Route::resource('vagas', \App\Http\Controllers\JobVacancyController::class)
        ->only(['create','store','edit','update','destroy']);
    
    Route::get('/job-vacancies/{id}/applications', [ApplicationController::class, 'byVacancy'])
        ->name('applications.byVacancy');
    
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])
        ->name('applications.updateStatus');
});

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth','verified'])
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
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
// Perfil público unificado (visualização)
Route::get('/profiles/{user}', [ProfileController::class, 'show'])->name('profiles.show');

// Perfil / dashboard
Route::middleware('auth')->group(function () {
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
    Route::get('/companies/{company}/vacancies', [CompanyController::class, 'vacancies'])
        ->name('companies.vacancies');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/job-vacancies/{id}/apply', [ApplicationController::class, 'store'])
        ->name('applications.store')
        ->middleware('can:isFreelancer');

    Route::get('/my-applications', [ApplicationController::class, 'index'])
        ->name('applications.index');
    
    Route::delete('/applications/{application}', [ApplicationController::class, 'cancel'])
        ->name('applications.cancel')
        ->middleware('can:isFreelancer');
});

// Rotas administrativas
Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/admin/freelancers', [FreelancerController::class, 'index'])->name('admin.freelancers');
    Route::get('/admin/companies', [CompanyController::class, 'index'])->name('admin.companies');
    Route::get('/admin/applications', [ApplicationController::class, 'adminIndex'])->name('admin.applications');
});

require __DIR__.'/auth.php';
