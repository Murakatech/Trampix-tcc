<?php

namespace App\Providers;

use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // During tests, prevent Vite from trying to read the manifest
        // file which may not exist in CI, causing errors when rendering
        // views with the @vite directive.
        if (app()->environment('testing')) {
            app()->bind(Vite::class, fn () => new class
            {
                public function __invoke(...$args)
                {
                    // Return empty string so Blade @vite() outputs nothing
                    // and tests can run without requiring built assets.
                    return '';
                }
            });
        }
    }
}
