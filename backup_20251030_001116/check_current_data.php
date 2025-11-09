<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Models\JobVacancy;
use App\Models\Application;
use App\Models\Freelancer;

echo "=== DADOS ATUAIS NO BANCO SQLITE ===\n\n";

echo "USUÁRIOS:\n";
$users = User::all(['email', 'name', 'role']);
foreach ($users as $user) {
    echo "- {$user->email} | {$user->name} | {$user->role}\n";
}

echo "\nEMPRESAS:\n";
$companies = Company::with('user')->get();
foreach ($companies as $company) {
    echo "- {$company->name} | {$company->user->email} | {$company->sector}\n";
}

echo "\nFREELANCERS:\n";
$freelancers = Freelancer::with('user')->get();
foreach ($freelancers as $freelancer) {
    echo "- {$freelancer->user->name} | {$freelancer->user->email} | {$freelancer->location}\n";
}

echo "\nVAGAS:\n";
$jobs = JobVacancy::with('company')->get();
foreach ($jobs as $job) {
    echo "- {$job->title} | {$job->company->name} | {$job->status}\n";
}

echo "\nCANDIDATURAS:\n";
$applications = Application::with(['freelancer.user', 'jobVacancy'])->get();
foreach ($applications as $app) {
    echo "- {$app->freelancer->user->name} -> {$app->jobVacancy->title} | {$app->status}\n";
}

echo "\n=== RESUMO ===\n";
echo "Total de usuários: " . User::count() . "\n";
echo "Total de empresas: " . Company::count() . "\n";
echo "Total de freelancers: " . Freelancer::count() . "\n";
echo "Total de vagas: " . JobVacancy::count() . "\n";
echo "Total de candidaturas: " . Application::count() . "\n";