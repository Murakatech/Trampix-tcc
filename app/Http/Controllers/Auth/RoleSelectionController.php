<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleSelectionController extends Controller
{
    /**
     * Exibe a tela de seleção de perfil
     */
    public function show(): View
    {
        $user = Auth::user();

        // Sempre exibe a tela de seleção - a lógica de redirecionamento
        // deve estar no AuthenticatedSessionController
        return view('auth.select_role', [
            'user' => $user,
            'hasFreelancer' => $user->isFreelancer(),
            'hasCompany' => $user->isCompany(),
        ]);
    }

    /**
     * Processa a seleção do perfil e define na sessão
     */
    public function select(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => 'required|in:freelancer,company',
        ]);

        $user = Auth::user();
        $selectedRole = $request->input('role');

        // Verifica se o usuário tem o perfil selecionado
        if ($selectedRole === 'freelancer' && ! $user->isFreelancer()) {
            return back()->with('error', 'Você não possui perfil de freelancer.');
        }

        if ($selectedRole === 'company' && ! $user->isCompany()) {
            return back()->with('error', 'Você não possui perfil de empresa.');
        }

        // Define o perfil ativo na sessão
        session(['active_role' => $selectedRole]);

        // Redireciona para o dashboard apropriado
        $dashboardRoute = $this->getDashboardRoute($selectedRole);

        return redirect()->route($dashboardRoute)
            ->with('success', 'Perfil selecionado com sucesso!');
    }

    /**
     * Limpa a seleção de perfil e volta para tela de seleção
     */
    public function switch(): RedirectResponse
    {
        $user = Auth::user();

        // Se tem apenas um perfil, não pode trocar
        if (($user->isFreelancer() && ! $user->isCompany()) ||
            ($user->isCompany() && ! $user->isFreelancer())) {
            return back()->with('info', 'Você possui apenas um perfil ativo.');
        }

        // Remove o perfil ativo da sessão
        session()->forget('active_role');

        // Redireciona para tela de seleção
        return redirect()->route('select-role.show')
            ->with('info', 'Selecione o perfil que deseja usar.');
    }

    /**
     * Determina a rota do dashboard baseada no perfil
     */
    private function getDashboardRoute(string $role): string
    {
        return match ($role) {
            'freelancer' => 'dashboard', // Por enquanto usa dashboard padrão
            'company' => 'dashboard',    // Por enquanto usa dashboard padrão
            default => 'dashboard'
        };
    }
}
