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
        $jobs = JobVacancy::where('company_id', $company->id)
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->paginate(5);

        // Estatísticas das vagas (jobStats)
        $jobStats = [
            'total' => $company->jobVacancies()->count(),
            'open' => $company->jobVacancies()->where('status', 'open')->count(),
            'closed' => $company->jobVacancies()->where('status', 'closed')->count(),
            'total_applications' => Application::whereHas('jobVacancy', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count(),
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
            'jobs',
            'jobStats',
            'recentApplications'
        ));
    }
}