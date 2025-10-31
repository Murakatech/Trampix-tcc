<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    // Minhas candidaturas (freelancer)
    public function index()
    {
        $freelancer = Auth::user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('dashboard')->with('error', 'Você precisa completar seu perfil de freelancer primeiro.');
        }

        $applications = Application::with('jobVacancy.company')
            ->where('freelancer_id', $freelancer->id)
            ->latest()
            ->get();

        return view('applications.index', compact('applications'));
    }

    // Aplicar em uma vaga (freelancer)
    public function store(Request $request, $jobVacancyId)
    {
        $freelancer = Auth::user()->freelancer;

        $exists = Application::where('job_vacancy_id', $jobVacancyId)
            ->where('freelancer_id', $freelancer->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Você já se candidatou a esta vaga.');
        }

        Application::create([
            'job_vacancy_id' => $jobVacancyId,
            'freelancer_id'  => $freelancer->id,
            'cover_letter'   => $request->input('cover_letter'),
            'status'         => 'pending',
        ]);

        return back()->with('success', 'Candidatura enviada com sucesso!');
    }

    // Gerenciar todas as candidaturas da empresa
    public function manage()
    {
        $company = Auth::user()->company;
        
        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'Perfil de empresa não encontrado.');
        }

        // Buscar todas as candidaturas para vagas da empresa
        $applications = Application::whereHas('jobVacancy', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with(['jobVacancy', 'freelancer.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Estatísticas das candidaturas
        $stats = [
            'total' => Application::whereHas('jobVacancy', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count(),
            'pending' => Application::whereHas('jobVacancy', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'pending')->count(),
            'accepted' => Application::whereHas('jobVacancy', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'accepted')->count(),
            'rejected' => Application::whereHas('jobVacancy', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'rejected')->count(),
        ];

        return view('applications.manage', compact('applications', 'stats'));
    }

    // Candidatos de uma vaga (empresa dona da vaga)
    public function byVacancy($jobVacancyId)
    {
        $company = Auth::user()->company ?? null;

        $vacancy = JobVacancy::where('id', $jobVacancyId)
            ->when($company, fn ($q) => $q->where('company_id', $company->id))
            ->with(['applications.freelancer.user'])
            ->firstOrFail();

        $applications = $vacancy->applications;

        return view('applications.by_vacancy', compact('vacancy', 'applications'));
    }
    
    // Atualizar status da candidatura (empresa)
    public function updateStatus(Request $request, \App\Models\Application $application)
    {
        $request->validate(['status' => 'required|in:pending,accepted,rejected']);

        $companyId = Auth::user()?->company?->id;
        if (!$companyId || $application->jobVacancy->company_id !== $companyId) {
            return back()->with('error', 'Você não tem permissão para alterar esta candidatura.');
        }

        $oldStatus = $application->status;
        $newStatus = $request->status;
        
        $application->update(['status' => $newStatus]);

        return back()->with('success', "Status alterado de '{$oldStatus}' para '{$newStatus}' com sucesso!");
    }

    public function cancel(Application $application)
    {
        // Verificar se a candidatura pertence ao freelancer logado
        if ($application->freelancer_id !== Auth::user()->freelancer->id) {
            return back()->with('error', 'Você não tem permissão para cancelar esta candidatura.');
        }

        // Só permitir cancelar candidaturas pendentes
        if ($application->status !== 'pending') {
            return back()->with('error', 'Só é possível cancelar candidaturas pendentes.');
        }

        // Deletar a candidatura
        $jobTitle = $application->jobVacancy->title;
        $application->delete();

        return back()->with('success', "Candidatura para '{$jobTitle}' cancelada com sucesso!");
    }

    // Área administrativa - todas as candidaturas
    public function adminIndex()
    {
        $applications = Application::with(['freelancer.user', 'jobVacancy.company'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'accepted' => Application::where('status', 'accepted')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        return view('admin.applications', compact('applications', 'stats'));
    }
}
