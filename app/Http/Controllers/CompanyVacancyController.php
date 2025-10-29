<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CompanyVacancyController extends Controller
{
    /**
     * Exibe apenas as vagas da empresa logada
     */
    public function index(Request $request)
    {
        // Verificar se é empresa
        if (!Gate::allows('isCompany')) {
            abort(403, 'Acesso negado. Apenas empresas podem acessar esta página.');
        }

        // Verificar se usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        try {
            // Buscar empresa do usuário logado
            $company = Auth::user()->company;
            
            if (!$company) {
                return redirect()->route('dashboard')->with('error', 'Perfil de empresa não encontrado.');
            }

            // Query base para vagas da empresa
            $query = JobVacancy::where('company_id', $company->id)->with('applications');

            // Filtros
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('contract_type')) {
                $query->where('contract_type', $request->contract_type);
            }

            if ($request->filled('location_type')) {
                $query->where('location_type', $request->location_type);
            }

            // Buscar vagas com paginação
            $vagas = $query->latest()->paginate(12);

            // Buscar categorias únicas para filtro
            $categories = JobVacancy::where('company_id', $company->id)
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');

            // Estatísticas
            $stats = [
                'total' => JobVacancy::where('company_id', $company->id)->count(),
                'active' => JobVacancy::where('company_id', $company->id)->where('status', 'active')->count(),
                'closed' => JobVacancy::where('company_id', $company->id)->where('status', 'closed')->count(),
                'total_applications' => \App\Models\Application::whereIn('job_vacancy_id', 
                    JobVacancy::where('company_id', $company->id)->pluck('id')
                )->count(),
            ];

            return view('company.vagas.index', compact('vagas', 'categories', 'stats', 'company'));

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar vagas da empresa: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Erro ao carregar vagas. Tente novamente.');
        }
    }

    /**
     * Exibe detalhes de uma vaga específica da empresa
     */
    public function show(JobVacancy $vaga)
    {
        // Verificar se é empresa
        if (!Gate::allows('isCompany')) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se a vaga pertence à empresa logada
        $company = Auth::user()->company;
        if (!$company || $vaga->company_id !== $company->id) {
            abort(403, 'Esta vaga não pertence à sua empresa.');
        }

        try {
            // Carregar vaga com aplicações
            $vaga->load(['applications.freelancer.user', 'company']);

            // Estatísticas da vaga
            $vagaStats = [
                'total_applications' => $vaga->applications->count(),
                'pending_applications' => $vaga->applications->where('status', 'pending')->count(),
                'accepted_applications' => $vaga->applications->where('status', 'accepted')->count(),
                'rejected_applications' => $vaga->applications->where('status', 'rejected')->count(),
            ];

            return view('company.vagas.show', compact('vaga', 'vagaStats'));

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar detalhes da vaga: ' . $e->getMessage());
            return redirect()->route('company.vagas.index')->with('error', 'Erro ao carregar vaga.');
        }
    }

    /**
     * Alterna status da vaga (ativa/fechada)
     */
    public function toggleStatus(JobVacancy $vaga)
    {
        // Verificar se é empresa
        if (!Gate::allows('isCompany')) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se a vaga pertence à empresa logada
        $company = Auth::user()->company;
        if (!$company || $vaga->company_id !== $company->id) {
            abort(403, 'Esta vaga não pertence à sua empresa.');
        }

        try {
            $newStatus = $vaga->status === 'active' ? 'closed' : 'active';
            $vaga->update(['status' => $newStatus]);

            $message = $newStatus === 'active' ? 'Vaga reativada com sucesso!' : 'Vaga fechada com sucesso!';
            
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Erro ao alterar status da vaga: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao alterar status da vaga.');
        }
    }
}