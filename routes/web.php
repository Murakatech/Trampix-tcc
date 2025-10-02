<?php

use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfilePhotoController;
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

// Styleguide (apenas para desenvolvimento)
Route::get('/styleguide', fn () => view('styleguide'))
    ->middleware(['auth'])
    ->name('styleguide');

// Público
Route::resource('vagas', JobVacancyController::class)->only(['index','show']);
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');

// Perfil / dashboard
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Perfil unificado
    Route::get('/profiles/{user}', [ProfileController::class, 'show'])->name('profiles.show');
    
    // Rotas para upload de foto de perfil
    Route::post('/profile/photo/upload', [ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo/delete', [ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');
    
    // Rotas para perfis de freelancer
    Route::get('/freelancers/profile', [FreelancerController::class, 'showOwn'])->name('freelancers.profile');
    Route::resource('freelancers', FreelancerController::class)
        ->only(['show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/freelancers/{freelancer}/download-cv', [FreelancerController::class, 'downloadCv'])
        ->name('freelancers.download-cv');
    
    // Rotas para perfis de empresa (autenticadas)
    Route::resource('companies', CompanyController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
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
