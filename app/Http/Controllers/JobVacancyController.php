<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class JobVacancyController extends Controller
{
    // Lista pública de vagas
    public function index(Request $request)
    {
        // Cache key baseado nos parâmetros de filtro
        $cacheKey = 'vagas_index_' . md5(serialize($request->all()));
        
        // Cache dos dados de filtros (válido por 30 minutos)
        $filterData = Cache::remember('vagas_filter_data', 1800, function () {
            return [
                'availableCategories' => JobVacancy::where('status', 'active')
                    ->whereNotNull('category')
                    ->distinct()
                    ->pluck('category')
                    ->sort()
                    ->values(),
                'contractTypes' => ['CLT', 'PJ', 'Freelancer', 'Estágio', 'Temporário'],
                'locationTypes' => ['Presencial', 'Remoto', 'Híbrido']
            ];
        });

        // Query otimizada com eager loading
        $query = JobVacancy::with(['company:id,name,user_id', 'applications:id,job_vacancy_id,freelancer_id'])
            ->select(['id', 'title', 'description', 'requirements', 'category', 'contract_type', 'location_type', 'salary_range', 'status', 'company_id', 'created_at'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');

        // Filtro por categorias (múltiplas seleções)
        if ($request->filled('categories')) {
            $categories = $request->categories;
            $query->whereIn('category', $categories);
        }

        // Filtro por tipo de contrato
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // Filtro por tipo de localização
        if ($request->filled('location_type')) {
            $query->where('location_type', $request->location_type);
        }

        // Busca por texto com índices otimizados
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%")
                  ->orWhereHas('company', function($companyQuery) use ($search) {
                      $companyQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Paginação com cache de contagem
        $vagas = $query->paginate(12)->withQueryString();

        // Cache da página por 10 minutos se não houver filtros específicos
        if (!$request->hasAny(['categories', 'contract_type', 'location_type', 'search'])) {
            $vagas = Cache::remember($cacheKey, 600, function () use ($query) {
                return $query->paginate(12);
            });
        }

        return view('vagas.index', array_merge(
            compact('vagas'), 
            $filterData
        ));
    }

    // Form de criação (somente logado + company)
    public function create()
    {
        // Permitir acesso para Empresa OU Admin
        if (! (Gate::allows('isCompany') || Gate::allows('isAdmin'))) {
            abort(403);
        }
        return view('vagas.create');
    }

    // Salva vaga
    public function store(Request $req)
    {
        // Permitir criação por Empresa OU Admin
        if (! (Gate::allows('isCompany') || Gate::allows('isAdmin'))) {
            abort(403);
        }

        $data = $req->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'requirements'  => 'nullable|string',
            'category'      => 'nullable|string|max:100',
            'contract_type' => 'nullable|in:PJ,CLT,Estágio,Freelance',
            'location_type' => 'nullable|in:Remoto,Híbrido,Presencial',
            'salary_range'  => 'nullable|string|max:100',
        ]);

        // Para Admin, permitir especificar company_id via request; se não houver, criar/usar perfil vinculado ao usuário
        if (Gate::allows('isAdmin') && $req->filled('company_id')) {
            $company = Company::findOrFail($req->input('company_id'));
        } else {
            $company = Company::firstOrCreate(
                ['user_id' => Auth::id()],
                ['name'    => Auth::user()->name]
            );
        }

        $data['company_id'] = $company->id;

        $vaga = JobVacancy::create($data);

        return redirect()->route('vagas.show', $vaga)->with('ok', 'Vaga criada.');
    }

    // Exibe vaga
    public function show(JobVacancy $vaga)
    {
        // Cache da vaga com relacionamentos por 15 minutos
        $cacheKey = "vaga_show_{$vaga->id}";
        
        $vagaWithRelations = Cache::remember($cacheKey, 900, function () use ($vaga) {
            return JobVacancy::with([
                'company:id,name,user_id,description,website,phone',
                'applications' => function($query) {
                    $query->select('id', 'job_vacancy_id', 'freelancer_id', 'status')
                          ->with('freelancer:id,user_id');
                }
            ])->find($vaga->id);
        });

        // Verificar se o usuário já aplicou para esta vaga (se for freelancer)
        $hasApplied = false;
        if (Auth::check() && Auth::user()->type === 'freelancer') {
            $freelancer = Auth::user()->freelancer;
            if ($freelancer) {
                $hasApplied = $vagaWithRelations->applications()
                    ->where('freelancer_id', $freelancer->id)
                    ->exists();
            }
        }

        return view('vagas.show', [
            'vaga' => $vagaWithRelations,
            'hasApplied' => $hasApplied
        ]);
    }

    // Edita vaga (somente dona)
    public function edit(JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || ($vaga->company?->user_id !== Auth::id())) abort(403);
        return view('vagas.edit', compact('vaga'));
    }

    // Atualiza vaga
    public function update(Request $req, JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || ($vaga->company?->user_id !== Auth::id())) abort(403);

        $data = $req->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'requirements'  => 'nullable|string',
            'category'      => 'nullable|string|max:100',
            'contract_type' => 'nullable|in:PJ,CLT,Estágio,Freelance',
            'location_type' => 'nullable|in:Remoto,Híbrido,Presencial',
            'salary_range'  => 'nullable|string|max:100',
            'status'        => 'nullable|in:active,closed',
        ]);

        $vaga->update($data);

        // Invalidar cache relacionado
        Cache::forget("vaga_show_{$vaga->id}");
        Cache::forget('vagas_filter_data');
        Cache::flush(); // Limpa cache de listagem com filtros

        return redirect()->route('vagas.show', $vaga)->with('ok', 'Vaga atualizada.');
    }

    // Remove vaga
    public function destroy(JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || ($vaga->company?->user_id !== Auth::id())) abort(403);
        
        // Invalidar cache antes de deletar
        Cache::forget("vaga_show_{$vaga->id}");
        Cache::forget('vagas_filter_data');
        Cache::flush(); // Limpa cache de listagem com filtros
        
        $vaga->delete();

        return redirect()->route('vagas.index')->with('ok', 'Vaga removida.');
    }
}
