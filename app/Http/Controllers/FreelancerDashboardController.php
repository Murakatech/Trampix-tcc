<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\JobVacancy;

class FreelancerDashboardController extends Controller
{
    /**
     * Exibe o dashboard específico para freelancers
     */
    public function index()
    {
        $user = auth()->user();
        $freelancer = $user->freelancer;

        if (!$freelancer) {
            return redirect()->route('dashboard')->with('error', 'Perfil de freelancer não encontrado.');
        }

        // Estatísticas de candidaturas
        $applications = Application::where('freelancer_id', $freelancer->id);
        $applicationsStats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        // Vagas recentes/recomendadas (últimas 6 vagas)
        $recentJobs = JobVacancy::where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Candidaturas recentes do freelancer
        $recentApplications = Application::where('freelancer_id', $freelancer->id)
            ->with('jobVacancy')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('freelancer.dashboard', compact(
            'freelancer',
            'applicationsStats',
            'recentJobs',
            'recentApplications'
        ));
    }
}