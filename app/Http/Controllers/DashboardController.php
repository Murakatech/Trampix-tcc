<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard unificado que adapta o conteúdo baseado no perfil do usuário
     */
    public function index()
    {
        // Early return: admins vão direto para o dashboard administrativo
        $user = auth()->user();
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Demais usuários usam a view unificada
        // A lógica de exibição é tratada no Blade com condicionais
        return view('dashboard');
    }
}