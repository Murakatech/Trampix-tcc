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
        
        // Detecta perfis disponíveis
        $hasFreelancer = $user->isFreelancer();
        $hasCompany = $user->isCompany();
        
        // Se não tem nenhum perfil ativo, vai para dashboard padrão
        if (!$hasFreelancer && !$hasCompany) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        
        // Se tem apenas um perfil, define automaticamente na sessão
        if ($hasFreelancer && !$hasCompany) {
            $request->session()->put('active_role', 'freelancer');
            return redirect()->intended(route('dashboard', absolute: false));
        }
        
        if ($hasCompany && !$hasFreelancer) {
            $request->session()->put('active_role', 'company');
            return redirect()->intended(route('dashboard', absolute: false));
        }
        
        // Se tem ambos os perfis, verifica se já tem um ativo na sessão
        if ($request->session()->has('active_role')) {
            $activeRole = $request->session()->get('active_role');
            
            // Valida se o perfil ativo ainda existe
            if (($activeRole === 'freelancer' && $hasFreelancer) || 
                ($activeRole === 'company' && $hasCompany)) {
                return redirect()->intended(route('dashboard', absolute: false));
            }
        }
        
        // Se tem ambos os perfis e não tem sessão ativa, vai para seleção
        return redirect()->route('select-role.show');
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
