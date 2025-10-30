<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redireciona para o dashboard específico baseado no perfil do usuário
     */
    public function index(Request $request): RedirectResponse|View
    {
        $user = $request->user();
        
        // Verificar se é admin
        if ($user->isAdmin()) {
            return view('dashboard');
        }
        
        // Verificar se tem perfil de freelancer ativo
        if ($user->isFreelancer()) {
            return redirect()->route('freelancer.dashboard');
        }
        
        // Verificar se tem perfil de empresa ativo
        if ($user->isCompany()) {
            return redirect()->route('company.dashboard');
        }
        
        // Se não tem nenhum perfil específico, mostrar dashboard genérico
        return view('dashboard');
    }
}