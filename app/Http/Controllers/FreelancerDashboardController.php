<?php

namespace App\Http\Controllers;

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

        if (! $freelancer) {
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

        // Candidaturas recentes do freelancer (últimas 7)
        $recentApplications = Application::where('freelancer_id', $freelancer->id)
            ->with(['jobVacancy', 'jobVacancy.company'])
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        // Vagas recomendadas baseadas nos setores do freelancer (match com categorias de vagas por nome)
        $freelancerCategories = $freelancer->sectors->pluck('name')->toArray();

        $recommendedJobs = JobVacancy::where('status', 'active')
            ->when(! empty($freelancerCategories), function ($query) use ($freelancerCategories) {
                $ids = \App\Models\ServiceCategory::whereIn('name', $freelancerCategories)->pluck('id');

                return $query->where(function ($q) use ($ids, $freelancerCategories) {
                    if ($ids->count() > 0) {
                        $q->whereIn('service_category_id', $ids);
                    }
                    $q->orWhereIn('category', $freelancerCategories);
                });
            })
            ->whereNotIn('id', function ($query) use ($freelancer) {
                $query->select('job_vacancy_id')
                    ->from('applications')
                    ->where('freelancer_id', $freelancer->id);
            })
            ->with(['company'])
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        // Se não há vagas nas categorias do freelancer, buscar vagas gerais
        if ($recommendedJobs->isEmpty()) {
            $recommendedJobs = JobVacancy::where('status', 'active')
                ->whereNotIn('id', function ($query) use ($freelancer) {
                    $query->select('job_vacancy_id')
                        ->from('applications')
                        ->where('freelancer_id', $freelancer->id);
                })
                ->with(['company'])
                ->orderBy('created_at', 'desc')
                ->limit(7)
                ->get();
        }

        return view('freelancer.dashboard', compact(
            'freelancer',
            'applicationsStats',
            'recommendedJobs',
            'recentApplications'
        ));
    }

    /**
     * Endpoint AJAX para atualizações em tempo real
     */
    public function getUpdates()
    {
        $user = auth()->user();
        $freelancer = $user->freelancer;

        if (! $freelancer) {
            return response()->json(['error' => 'Freelancer não encontrado'], 404);
        }

        // Estatísticas atualizadas
        $applications = Application::where('freelancer_id', $freelancer->id);
        $applicationsStats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        // Verificar se há novas candidaturas (últimas 24 horas)
        $newApplicationsCount = Application::where('freelancer_id', $freelancer->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        // Verificar se há novas vagas recomendadas (últimas 24 horas)
        $freelancerSectorNames = $freelancer->sectors->pluck('name')->toArray();
        $sectorMappedCategoryIds = \App\Models\ServiceCategory::whereIn('name', $freelancerSectorNames)->pluck('id');
        $newRecommendedJobsCount = JobVacancy::where('status', 'open')
            ->when($sectorMappedCategoryIds->count() > 0, function ($query) use ($sectorMappedCategoryIds) {
                return $query->whereIn('service_category_id', $sectorMappedCategoryIds);
            })
            ->where('created_at', '>=', now()->subDay())
            ->whereNotIn('id', function ($query) use ($freelancer) {
                $query->select('job_vacancy_id')
                    ->from('applications')
                    ->where('freelancer_id', $freelancer->id);
            })
            ->count();

        return response()->json([
            'applicationsStats' => $applicationsStats,
            'newApplicationsCount' => $newApplicationsCount,
            'newRecommendedJobsCount' => $newRecommendedJobsCount,
            'lastUpdate' => now()->format('H:i:s'),
        ]);
    }
}
