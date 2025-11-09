<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class JobVacancyController extends Controller
{
    // Lista pública de vagas
    public function index(Request $request)
    {
        // Garantir papel ativo correto na tela de busca de vagas
        // Se o usuário autenticado possuir perfil de Freelancer, definimos o papel ativo
        // para "freelancer" nesta tela, para que a sidebar reflita as opções corretas.
        if (Auth::check() && optional(Auth::user())->isFreelancer()) {
            session(['active_role' => 'freelancer']);
        }

        // Cache key baseado nos parâmetros de filtro
        $cacheKey = 'vagas_index_' . md5(serialize($request->all()));
        
        // Cache dos dados de filtros (válido por 30 minutos)
        $filterData = Cache::remember('vagas_filter_data', 1800, function () {
            // Todas as categorias cadastradas no sistema (ordenadas por nome)
            $allCategoryNames = Category::orderBy('name')->pluck('name')->toArray();

            // Complemento com categorias legadas existentes nas vagas (strings antigas)
            $namesFromLegacy = JobVacancy::where('status', 'active')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->toArray();

            // Disponibilizar no dropdown: todas cadastradas + legadas não cadastradas
            $availableCategories = collect($allCategoryNames)
                ->merge($namesFromLegacy)
                ->unique()
                ->sort()
                ->values();

            return [
                'availableCategories' => $availableCategories,
                'contractTypes' => ['CLT', 'PJ', 'Freelance', 'Estágio', 'Temporário'],
                'locationTypes' => ['Presencial', 'Remoto', 'Híbrido']
            ];
        });

        // Query otimizada com eager loading
        $query = JobVacancy::with(['company:id,name,user_id', 'applications:id,job_vacancy_id,freelancer_id', 'category:id,name'])
            ->select(['id', 'title', 'description', 'requirements', 'category', 'category_id', 'contract_type', 'location_type', 'salary_range', 'status', 'company_id', 'created_at'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');

        // Ocultar vagas já aplicadas para usuários freelancers
        if (Auth::check() && Gate::allows('isFreelancer')) {
            $freelancerId = optional(Auth::user()->freelancer)->id;
            if ($freelancerId) {
                $query->whereDoesntHave('applications', function($q) use ($freelancerId) {
                    $q->where('freelancer_id', $freelancerId);
                });
                // Diferenciar cache por usuário autenticado
                $cacheKey .= '_user_' . Auth::id();
            }
        }

        // Filtro por categorias
        // Suporta múltiplas seleções via "categories[]" e seleção simples via "category"
        if ($request->filled('categories') || $request->filled('category')) {
            $categories = collect((array) $request->get('categories', []));
            if ($request->filled('category')) {
                $categories = $categories->merge([(string) $request->get('category')]);
            }

            $categories = $categories->filter()->unique()->values()->all();
            if (!empty($categories)) {
                $categoryIds = Category::whereIn('name', $categories)->pluck('id');
                $query->where(function($q) use ($categories, $categoryIds) {
                    if ($categoryIds->count() > 0) {
                        $q->whereIn('category_id', $categoryIds);
                    }
                    // Fallback para dados antigos com string
                    $q->orWhereIn('category', $categories);
                });
            }
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
        // Evitar cache compartilhado para freelancers autenticados (lista depende do usuário)
        if (!$request->hasAny(['categories', 'contract_type', 'location_type', 'search'])
            && (!Auth::check() || !Gate::allows('isFreelancer'))) {
            $vagas = Cache::remember($cacheKey, 600, function () use ($query) {
                return $query->paginate(12);
            });
        }

        return view('vagas.index', array_merge([
            'vagas' => $vagas,
            'selectedCategories'   => (array) $request->get('categories', $request->filled('category') ? [$request->get('category')] : []),
            'selectedContractType' => $request->get('contract_type'),
            'selectedLocationType' => $request->get('location_type'),
            'search'               => $request->get('search'),
        ], $filterData));
    }

    // Formulário de criação de vaga (empresa/admin)
    public function create()
    {
        if (! Gate::allows('isCompany') && ! Gate::allows('isAdmin')) abort(403);

        $categories = Category::orderBy('name')->get();
        $companies  = Gate::allows('isAdmin') ? Company::orderBy('name')->select('id','name')->get() : null;

        return view('vagas.create', compact('categories','companies'));
    }

    // Salva nova vaga
    public function store(Request $req)
    {
        if (! Gate::allows('isCompany') && ! Gate::allows('isAdmin')) abort(403);

        $data = $req->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'requirements'  => 'nullable|string',
            'category_id'   => ['nullable','exists:categories,id'],
            'contract_type' => 'nullable|in:PJ,CLT,Estágio,Freelance',
            'location_type' => 'nullable|in:Remoto,Híbrido,Presencial',
            'salary_range'  => 'nullable|string|max:100',
            'company_id'    => ['nullable','exists:companies,id'],
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

        // Garantir category_id e manter compatibilidade com campo legacy 'category'
        if (empty($data['category_id']) && $req->filled('category')) {
            $legacyName = $req->input('category');
            $data['category_id'] = Category::where('name', $legacyName)->value('id');
        }

        // Status padrão ativo
        $data['status'] = $data['status'] ?? 'active';

        $vaga = JobVacancy::create($data);

        // Invalidar caches relacionados
        Cache::forget('vagas_filter_data');
        Cache::flush();

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
        $categories = Category::orderBy('name')->get();
        return view('vagas.edit', compact('vaga','categories'));
    }

    // Atualiza vaga
    public function update(Request $req, JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || ($vaga->company?->user_id !== Auth::id())) abort(403);

        $data = $req->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'requirements'  => 'nullable|string',
            'category_id'   => ['nullable','exists:categories,id'],
            'contract_type' => 'nullable|in:PJ,CLT,Estágio,Freelance',
            'location_type' => 'nullable|in:Remoto,Híbrido,Presencial',
            'salary_range'  => 'nullable|string|max:100',
            'status'        => 'nullable|in:active,closed',
        ]);

        // Garantir category_id (compatibilidade com campo legacy 'category')
        if (empty($data['category_id']) && $req->filled('category')) {
            $legacyName = $req->input('category');
            $data['category_id'] = Category::where('name', $legacyName)->value('id');
        }

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

    // Endpoint de sugestões para busca suave
    public function suggest(Request $request)
    {
        try {
            $q = trim((string) $request->get('search'));
            if (strlen($q) < 2) return response()->json(['suggestions' => []], 200, [], JSON_UNESCAPED_UNICODE);

            // Busca semelhante: dividir termos e usar LIKE em múltiplos campos
            $terms = collect(preg_split('/\s+/', $q))->filter()->values();
            $query = JobVacancy::query()->where('status','active');
            $query->where(function($outer) use ($terms){
                foreach ($terms as $t) {
                    $outer->orWhere('title', 'like', "%{$t}%")
                          ->orWhere('description', 'like', "%{$t}%")
                          ->orWhere('requirements', 'like', "%{$t}%");
                }
            });
            // Incluir nomes de empresa relacionados
            $query->orWhereHas('company', function($c) use ($terms){
                $c->where(function($cc) use ($terms){
                    foreach ($terms as $t) {
                        $cc->orWhere('name', 'like', "%{$t}%");
                    }
                });
            });

            // Coletar títulos e empresas distintos como sugestões
            $titles = $query->limit(20)->pluck('title')->unique()->take(10)->values()->all();

            // Sugestões de empresas e categorias também
            $companies = \App\Models\Company::where(function($c) use ($terms){
                foreach ($terms as $t) { $c->orWhere('name','like', "%{$t}%"); }
            })->limit(10)->pluck('name')->all();

            $categories = \App\Models\Category::where(function($cat) use ($terms){
                foreach ($terms as $t) { $cat->orWhere('name','like', "%{$t}%"); }
            })->limit(10)->pluck('name')->all();

            // Mesclar e remover duplicados, priorizando títulos
            $suggestions = collect($titles)->merge($companies)->merge($categories)
                ->unique()->take(12)->values()->all();

            return response()->json(['suggestions' => $suggestions], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            \Log::error('Erro no suggest de vagas: '.$e->getMessage());
            return response()->json(['suggestions' => []], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
