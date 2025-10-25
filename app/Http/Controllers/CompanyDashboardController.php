<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobVacancy;
use App\Models\Application;

class CompanyDashboardController extends Controller
{
    /**
     * Exibe o dashboard específico para empresas
     */
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Perfil de empresa não encontrado.');
        }

        // Vagas da empresa com contagem de candidatos
        $jobVacancies = JobVacancy::where('company_id', $company->id)
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->get();

        // Estatísticas gerais
        $stats = [
            'total_jobs' => $jobVacancies->count(),
            'open_jobs' => $jobVacancies->where('status', 'open')->count(),
            'closed_jobs' => $jobVacancies->where('status', 'closed')->count(),
            'total_applications' => $jobVacancies->sum('applications_count'),
        ];

        // Candidaturas recentes para vagas da empresa
        $recentApplications = Application::whereHas('jobVacancy', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['jobVacancy', 'freelancer.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('company.dashboard', compact(
            'company',
            'jobVacancies',
            'stats',
            'recentApplications'
        ));
    }
}