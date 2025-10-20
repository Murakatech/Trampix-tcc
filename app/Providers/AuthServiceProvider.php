<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Freelancer;
use App\Models\Company;
use App\Policies\FreelancerPolicy;
use App\Policies\CompanyPolicy;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register model policies
        Gate::policy(Freelancer::class, FreelancerPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);

        // Gates baseados em perfis ativos (novo sistema)
        Gate::define('isFreelancer', function ($user) {
            return $user->freelancer()->where('is_active', true)->exists();
        });

        Gate::define('isCompany', function ($user) {
            return $user->company()->where('is_active', true)->exists();
        });

        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        // Gates para verificar se pode criar perfis
        Gate::define('canCreateFreelancerProfile', function ($user) {
            return $user->isAdmin() || !$user->hasActiveProfile('freelancer');
        });

        Gate::define('canCreateCompanyProfile', function ($user) {
            return $user->isAdmin() || !$user->hasActiveProfile('company');
        });

        // Gates para editar perfis específicos
        Gate::define('editFreelancerProfile', function ($user, $freelancer = null) {
            if ($user->isAdmin()) return true;
            if (!$freelancer) return $user->isFreelancer();
            return $user->id === $freelancer->user_id;
        });

        Gate::define('editCompanyProfile', function ($user, $company = null) {
            if ($user->isAdmin()) return true;
            if (!$company) return $user->isCompany();
            return $user->id === $company->user_id;
        });

        // Gates para funcionalidades específicas
        Gate::define('applyToJobs', function ($user) {
            return $user->isFreelancer();
        });

        Gate::define('postJobs', function ($user) {
            return $user->isCompany();
        });

        Gate::define('manageApplications', function ($user, $jobVacancy = null) {
            if ($user->isAdmin()) return true;
            if (!$jobVacancy) return $user->isCompany();
            return $user->isCompany() && $user->companies()->where('is_active', true)->whereHas('vacancies', function($q) use ($jobVacancy) {
                $q->where('id', $jobVacancy->id);
            })->exists();
        });
    }
}
