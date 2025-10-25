<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Redireciona o usuário para o dashboard apropriado baseado no seu perfil
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isFreelancer()) {
            return redirect()->route('freelancer.dashboard');
        } elseif ($user->isCompany()) {
            return redirect()->route('company.dashboard');
        } else {
            // Para usuários sem perfil específico ou admins, vai para dashboard genérico
            return redirect()->route('dashboard');
        }
    }
}