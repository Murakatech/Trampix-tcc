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

        // Vagas recomendadas baseadas nos SEGMENTOS do freelancer (categoria da vaga ou segmento da empresa)
        $segmentIds = [];
        if ($freelancer->segment_id) { $segmentIds[] = (int) $freelancer->segment_id; }
        try { $segmentIds = array_unique(array_filter(array_merge($segmentIds, $freelancer->segments()->pluck('segments.id')->all()))); } catch (\Throwable $e) {}

        $recommendedJobs = JobVacancy::where('status', 'active')
            ->when(! empty($segmentIds), function ($query) use ($segmentIds) {
                $query->where(function ($q) use ($segmentIds) {
                    $q->whereHas('category', function ($cq) use ($segmentIds) {
                        $cq->whereIn('segment_id', $segmentIds);
                    })->orWhereHas('company', function ($compQ) use ($segmentIds) {
                        $compQ->whereIn('segment_id', $segmentIds);
                    });
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

        // Verificar se há novas vagas recomendadas (últimas 24 horas) baseadas em segmentos
        $segmentIds = [];
        if ($freelancer->segment_id) { $segmentIds[] = (int) $freelancer->segment_id; }
        try { $segmentIds = array_unique(array_filter(array_merge($segmentIds, $freelancer->segments()->pluck('segments.id')->all()))); } catch (\Throwable $e) {}
        $newRecommendedJobsCount = JobVacancy::where('status', 'active')
            ->when(! empty($segmentIds), function ($query) use ($segmentIds) {
                $query->where(function ($q) use ($segmentIds) {
                    $q->whereHas('category', function ($cq) use ($segmentIds) {
                        $cq->whereIn('segment_id', $segmentIds);
                    })->orWhereHas('company', function ($compQ) use ($segmentIds) {
                        $compQ->whereIn('segment_id', $segmentIds);
                    });
                });
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
