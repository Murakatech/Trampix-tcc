<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Redireciona o usuário para o dashboard unificado
     */
    public function index()
    {
        // Todos os usuários são redirecionados para o dashboard unificado
        // A lógica de exibição é tratada no Blade baseada no perfil
        return redirect()->route('dashboard');
    }
}