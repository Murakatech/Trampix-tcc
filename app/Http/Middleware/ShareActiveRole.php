<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareActiveRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $activeRole = session('active_role');

            // Valida se o active_role ainda é válido
            if ($activeRole) {
                $isValidRole = match ($activeRole) {
                    'freelancer' => $user->isFreelancer(),
                    'company' => $user->isCompany(),
                    default => false
                };

                // Se o role não é mais válido, remove da sessão
                if (! $isValidRole) {
                    session()->forget('active_role');
                    $activeRole = null;
                }
            }

            // Compartilha dados com todas as views
            View::share([
                'activeRole' => $activeRole,
                'hasFreelancerProfile' => $user->isFreelancer(),
                'hasCompanyProfile' => $user->isCompany(),
                'canSwitchRoles' => $user->isFreelancer() && $user->isCompany(),
                'currentUser' => $user,
            ]);
        }

        return $next($request);
    }
}
