<?php

use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

// garante que {vaga} só aceite números
Route::pattern('vaga', '[0-9]+');

// Home
Route::get('/', [JobVacancyController::class, 'index'])->name('home');

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

// Público
Route::resource('vagas', JobVacancyController::class)->only(['index','show']);

// Perfil / dashboard
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rotas para perfis de freelancer
    Route::get('/freelancers/profile', [FreelancerController::class, 'showOwn'])->name('freelancers.profile');
    Route::resource('freelancers', FreelancerController::class)
        ->only(['show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/freelancers/{freelancer}/download-cv', [FreelancerController::class, 'downloadCv'])
        ->name('freelancers.download-cv');
    
    // Rotas para perfis de empresa
    Route::get('/companies', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
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
});

require __DIR__.'/auth.php';
