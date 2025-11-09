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

        // Exibir apenas candidaturas que ainda fazem sentido na tela principal:
        // - Ocultar rejeitadas (vão para Trabalhos Finalizados)
        // - Ocultar finalizadas com avaliação enviada pelo freelancer
        $applications = Application::with('jobVacancy.company')
            ->where('freelancer_id', $freelancer->id)
            ->where(function($q) {
                $q->where('status', '!=', 'rejected')
                  ->where(function($q2) {
                      $q2->where('status', '!=', 'ended')
                         ->orWhereNull('evaluated_by_freelancer_at');
                  });
            })
            ->latest()
            ->get();

        // Contagem de rejeições não reconhecidas para mostrar o aviso até o freela apertar "OK"
        $unackRejectedCount = Application::where('freelancer_id', $freelancer->id)
            ->where('status', 'rejected')
            ->where(function($q) {
                $q->whereNull('rejected_acknowledged')
                  ->orWhere('rejected_acknowledged', false);
            })
            ->count();

        return view('applications.index', compact('applications', 'unackRejectedCount'));
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
            // Ocultar finalizadas que já foram avaliadas pela empresa (migram para Trabalhos Finalizados)
            ->where(function($q) {
                $q->where('status', '!=', 'ended')
                  ->orWhere(function($q2) {
                      $q2->where('status', 'ended')->whereNull('evaluated_by_company_at');
                  });
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
    public function byVacancy($id)
    {
        $company = Auth::user()->company;

        // Garantir que a vaga pertence à empresa logada e carregar relacionamentos
        $jobVacancy = JobVacancy::where('company_id', $company->id)
            ->with(['applications.freelancer.user'])
            ->find($id);

        if (!$jobVacancy) {
            abort(404, 'Vaga não encontrada ou não pertence à empresa.');
        }

        // Compatibilidade com a view
        $applications = $jobVacancy->applications;
        return view('applications.by_vacancy', [
            'jobVacancy' => $jobVacancy,
            'vacancy' => $jobVacancy,
            'applications' => $applications,
        ]);
    }
    
    // Atualizar status da candidatura (empresa)
    public function updateStatus(Request $request, \App\Models\Application $application)
    {
        $request->validate(['status' => 'required|in:pending,accepted,rejected,ended']);

        $companyId = Auth::user()?->company?->id;
        if (!$companyId || $application->jobVacancy->company_id !== $companyId) {
            return back()->with('error', 'Você não tem permissão para alterar esta candidatura.');
        }

        $oldStatus = $application->status;
        $newStatus = $request->status;
        // Mapear rótulos em PT-BR para exibição consistente em mensagens
        $statusLabels = [
            'pending' => 'Pendente',
            'accepted' => 'Aceito',
            'rejected' => 'Rejeitado',
            'ended' => 'Finalizado',
        ];
        
        $application->update(['status' => $newStatus]);

        // Mensagem customizada para finalização de contrato
        $finalize = $request->boolean('finalize');
        if ($finalize && $oldStatus === 'accepted' && $newStatus === 'ended') {
            return back()->with('success', 'Parceria finalizada com sucesso!');
        }

        $oldLabel = $statusLabels[$oldStatus] ?? ucfirst($oldStatus);
        $newLabel = $statusLabels[$newStatus] ?? ucfirst($newStatus);
        return back()->with('success', "Status alterado de '{$oldLabel}' para '{$newLabel}' com sucesso!");
    }

    /**
     * Trabalhos finalizados (empresa e freelancer) — roteamento por papel ativo.
     */
    public function finishedIndex()
    {
        $user = Auth::user();

        if ($user?->company) {
            $company = $user->company;
            $applications = Application::whereHas('jobVacancy', function($q) use ($company) {
                    $q->where('company_id', $company->id);
                })
                ->where('status', 'ended')
                ->whereNotNull('evaluated_by_company_at')
                ->with(['jobVacancy.company', 'freelancer.user'])
                ->latest()
                ->paginate(12);

            return view('finished.company', compact('applications', 'company'));
        }

        if ($user?->freelancer) {
            $freelancer = $user->freelancer;
            $applications = Application::where('freelancer_id', $freelancer->id)
                ->where(function($q) {
                    $q->where('status', 'rejected')
                      ->orWhere(function($q2) {
                          $q2->where('status', 'ended')->whereNotNull('evaluated_by_freelancer_at');
                      });
                })
                ->with(['jobVacancy.company.user'])
                ->latest()
                ->paginate(12);

            return view('finished.freelancer', compact('applications', 'freelancer'));
        }

        return redirect()->route('dashboard')->with('error', 'Perfil não identificado para acessar Trabalhos Finalizados.');
    }

    /**
     * Freelancer reconhece rejeições para ocultar aviso em "Minhas Candidaturas".
     */
    public function acknowledgeAllRejections()
    {
        $user = Auth::user();
        if (!$user?->freelancer) {
            abort(403, 'Apenas freelancers podem reconhecer rejeições.');
        }

        \App\Models\Application::where('freelancer_id', $user->freelancer->id)
            ->where('status', 'rejected')
            ->update(['rejected_acknowledged' => true]);

        return redirect()->back()->with('success', 'Aviso de rejeição reconhecido.');
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

    /**
     * Freela se demite (encerra parceria) mudando status de 'accepted' para 'rejected'.
     */
    public function resign(\App\Models\Application $application)
    {
        $user = Auth::user();
        if (!$user?->freelancer || $application->freelancer_id !== $user->freelancer->id) {
            return back()->with('error', 'Você não tem permissão para encerrar esta parceria.');
        }

        if ($application->status !== 'accepted') {
            return back()->with('error', 'A parceria não está ativa.');
        }

        $application->update(['status' => 'ended']);
        return back()->with('success', 'Parceria finalizada com sucesso!');
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
