<?php

use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// garante que {vaga} só aceite números
Route::pattern('vaga', '[0-9]+');

// Home
Route::get('/', [JobVacancyController::class, 'index'])->name('home');

// Protegido (auth + empresa) — registre ANTES
Route::middleware(['auth','can:isCompany'])->group(function () {
    Route::resource('vagas', \App\Http\Controllers\JobVacancyController::class)
        ->only(['create','store','edit','update','destroy']);
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
});

require __DIR__.'/auth.php';
