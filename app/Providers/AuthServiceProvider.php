<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('isFreelancer', fn($u) => in_array($u->role, ['freelancer','admin']));
        Gate::define('isCompany',    fn($u) => in_array($u->role, ['company','admin']));
        Gate::define('isAdmin',      fn($u) => $u->role === 'admin');
    }
}
