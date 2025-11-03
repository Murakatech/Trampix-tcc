<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        
        // Admins não precisam de seleção de perfil nem criação de perfis
        if ($user->isAdmin()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Determinar redirecionamento baseado nos perfis do usuário
        $hasFreelancer = $user->isFreelancer();
        $hasCompany = $user->isCompany();
        $activeRole = session('active_role');

        // Se não tem nenhum perfil, redirecionar para seleção de perfil (criação)
        if (!$hasFreelancer && !$hasCompany) {
            return redirect()->route('profile.selection');
        }

        // Se tem ambos os perfis mas não tem active_role na sessão, redirecionar para seleção de papel
        if ($hasFreelancer && $hasCompany && !$activeRole) {
            return redirect()->route('select-role.show');
        }

        // Se tem active_role válido ou apenas um perfil, redirecionar para dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
