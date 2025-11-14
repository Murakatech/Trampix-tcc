<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobVacancyRequest;
use App\Http\Requests\UpdateJobVacancyRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\JobVacancy;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
        $cacheKey = 'vagas_index_'.md5(serialize($request->all()));

        // Cache dos dados de filtros (válido por 30 minutos)
        $filterData = Cache::remember('vagas_filter_data', 1800, function () {
            $availableCategories = Category::orderBy('name')->get();

            return [
                'availableCategories' => $availableCategories,
                'locationTypes' => ['Presencial', 'Remoto', 'Híbrido'],
                'segments' => Segment::where('active', true)->orderBy('name')->select('id', 'name')->get(),
            ];
        });

        // Query otimizada com eager loading
        $query = JobVacancy::with([
            'company:id,name,user_id,segment_id',
            'company.segment:id,name',
            'applications:id,job_vacancy_id,freelancer_id',
            'category:id,name,segment_id',
            'category.segment:id,name',
        ])
            ->select(['id', 'title', 'description', 'requirements', 'category_id', 'location_type', 'salary_range', 'status', 'company_id', 'created_at'])
            ->publicList()
            ->orderBy('created_at', 'desc');

        // Ocultar vagas já aplicadas para usuários freelancers
        if (Auth::check() && Gate::allows('isFreelancer')) {
            $freelancerId = optional(Auth::user()->freelancer)->id;
            if ($freelancerId) {
                $query->notAppliedBy($freelancerId);
                // Diferenciar cache por usuário autenticado
                $cacheKey .= '_user_'.Auth::id();
            }
        }

        // Filtro por categoria via category_id
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->category_id);
            $cacheKey .= '_cat_'.(int) $request->category_id;
        }

        // Filtro por segmento (novo)
        if ($request->filled('segment_id')) {
            $query->filterSegment((int) $request->get('segment_id'));
        }

        // Removido: filtro por tipo de contrato (todos são freelance)

        // Filtro por tipo de localização
        $query->locationType($request->filled('location_type') ? $request->location_type : null);

        // Busca por texto com índices otimizados
        $query->search($request->filled('search') ? $request->search : null);

        // Ordenação por avaliação da empresa (média das avaliações feitas pelos freelancers)
        if ($request->filled('rating_order') && in_array($request->get('rating_order'), ['asc', 'desc'])) {
            // Subquery: média de freelancer_rating (inteiro) por company_id
            $companyRatingsSub = \App\Models\Application::query()
                ->selectRaw('job_vacancies.company_id as company_id, AVG(applications.freelancer_rating) as company_public_rating_avg')
                ->join('job_vacancies', 'applications.job_vacancy_id', '=', 'job_vacancies.id')
                ->where('applications.status', 'ended')
                ->whereNotNull('applications.freelancer_rating')
                ->groupBy('job_vacancies.company_id');

            // Join da subquery para adicionar a coluna calculada e ordenar
            $query->leftJoinSub($companyRatingsSub, 'company_ratings', function ($join) {
                $join->on('job_vacancies.company_id', '=', 'company_ratings.company_id');
            })
                ->addSelect('company_ratings.company_public_rating_avg');

            $orderDir = $request->get('rating_order');
            if ($orderDir === 'desc') {
                $query->orderByDesc('company_public_rating_avg')->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('company_public_rating_avg', 'asc')->orderBy('created_at', 'desc');
            }
        }

        // Paginação com cache de contagem
        $vagas = $query->paginate(12)->withQueryString();

        // Cache da página por 10 minutos se não houver filtros específicos
        // Evitar cache compartilhado para freelancers autenticados (lista depende do usuário)
        if (! $request->hasAny(['category_id', 'location_type', 'search'])
            && (! Auth::check() || ! Gate::allows('isFreelancer'))) {
            $vagas = Cache::remember($cacheKey, 600, function () use ($query) {
                return $query->paginate(12);
            });
        }

        return view('vagas.index', array_merge([
            'vagas' => $vagas,
            'selectedCategoryId' => $request->get('category_id'),
            'selectedLocationType' => $request->get('location_type'),
            'search' => $request->get('search'),
        ], $filterData));
    }

    /**
     * Retorna categorias (nome) por segmento para popular o filtro dinamicamente
     */
    public function categoriesBySegment(Segment $segment)
    {
        $categories = Category::where('segment_id', $segment->id)
            ->where('active', true)
            ->orderBy('name')
            ->select('id', 'name')
            ->get();

        return response()->json([
            'segment' => [
                'id' => $segment->id,
                'name' => $segment->name,
            ],
            'categories' => $categories,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // Formulário de criação de vaga (empresa/admin)
    public function create()
    {
        if (! Gate::allows('isCompany') && ! Gate::allows('isAdmin')) {
            abort(403);
        }

        $categories = Category::where('active', true)->orderBy('name')->get();
        $segments = Segment::where('active', true)->orderBy('name')->get();
        $companies = Gate::allows('isAdmin') ? Company::orderBy('name')->select('id', 'name')->get() : null;

        return view('vagas.create', compact('categories', 'companies', 'segments'));
    }

    // Salva nova vaga
    public function store(StoreJobVacancyRequest $req)
    {
        if (! Gate::allows('isCompany') && ! Gate::allows('isAdmin')) {
            abort(403);
        }

        // Validação via FormRequest
        $validated = $req->validated();

        // Para Admin, permitir especificar company_id via request; se não houver, criar/usar perfil vinculado ao usuário
        if (Gate::allows('isAdmin') && $req->filled('company_id')) {
            $company = Company::findOrFail($req->input('company_id'));
        } else {
            $company = Company::firstOrCreate(
                ['user_id' => Auth::id()],
                ['name' => Auth::user()->name]
            );
        }

        $data = $validated;
        $data['company_id'] = $company->id;
        // Resolver category_id a partir do campo legacy, e validar relação categoria->segmento
        $this->resolveCategoryIdFromLegacy($req, $data);
        $invalidRedirect = $this->validateCategoryBelongsToSegment($req, $data);
        if ($invalidRedirect) {
            return $invalidRedirect;
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
                'applications' => function ($query) {
                    $query->select('id', 'job_vacancy_id', 'freelancer_id', 'status')
                        ->with('freelancer:id,user_id');
                },
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
            'hasApplied' => $hasApplied,
        ]);
    }

    // Edita vaga (somente dona)
    public function edit(JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || ($vaga->company?->user_id !== Auth::id())) {
            abort(403);
        }
        $categories = Category::where('active', true)->orderBy('name')->get();
        $segments = Segment::where('active', true)->orderBy('name')->get();

        return view('vagas.edit', compact('vaga', 'categories', 'segments'));
    }

    // Atualiza vaga
    public function update(UpdateJobVacancyRequest $req, JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || ($vaga->company?->user_id !== Auth::id())) {
            abort(403);
        }

        $validated = $req->validated();

        $data = $validated;
        // Resolver category_id a partir do campo legacy, e validar relação categoria->segmento
        $this->resolveCategoryIdFromLegacy($req, $data);
        $invalidRedirect = $this->validateCategoryBelongsToSegment($req, $data);
        if ($invalidRedirect) {
            return $invalidRedirect;
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
        if (! Gate::allows('isCompany') || ($vaga->company?->user_id !== Auth::id())) {
            abort(403);
        }

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
            if (strlen($q) < 2) {
                return response()->json(['suggestions' => []], 200, [], JSON_UNESCAPED_UNICODE);
            }

            // Busca semelhante: dividir termos e usar LIKE em múltiplos campos
            $terms = collect(preg_split('/\s+/', $q))->filter()->values();
            $query = JobVacancy::query()->where('status', 'active');
            $query->where(function ($outer) use ($terms) {
                foreach ($terms as $t) {
                    $outer->orWhere('title', 'like', "%{$t}%")
                        ->orWhere('description', 'like', "%{$t}%")
                        ->orWhere('requirements', 'like', "%{$t}%");
                }
            });
            // Incluir nomes de empresa relacionados
            $query->orWhereHas('company', function ($c) use ($terms) {
                $c->where(function ($cc) use ($terms) {
                    foreach ($terms as $t) {
                        $cc->orWhere('name', 'like', "%{$t}%");
                    }
                });
            });

            // Coletar títulos e empresas distintos como sugestões
            $titles = $query->limit(20)->pluck('title')->unique()->take(10)->values()->all();

            // Sugestões de empresas e categorias também
            $companies = \App\Models\Company::where(function ($c) use ($terms) {
                foreach ($terms as $t) {
                    $c->orWhere('name', 'like', "%{$t}%");
                }
            })->limit(10)->pluck('name')->all();

            $categories = \App\Models\Category::where(function ($cat) use ($terms) {
                foreach ($terms as $t) {
                    $cat->orWhere('name', 'like', "%{$t}%");
                }
            })->limit(10)->pluck('name')->all();

            // Mesclar e remover duplicados, priorizando títulos
            $suggestions = collect($titles)->merge($companies)->merge($categories)
                ->unique()->take(12)->values()->all();

            return response()->json(['suggestions' => $suggestions], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error('Erro no suggest de vagas: '.$e->getMessage());

            return response()->json(['suggestions' => []], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Helpers privados para reduzir duplicação: categoria legacy e validação de pertencimento ao segmento
     */
    private function resolveCategoryIdFromLegacy(Request $req, array &$data): void
    {
        if (empty($data['category_id']) && $req->filled('category')) {
            $legacyName = $req->input('category');
            $data['category_id'] = Category::where('name', $legacyName)->value('id');
        }
    }

    private function validateCategoryBelongsToSegment(Request $req, array $data)
    {
        if (empty($data['category_id'])) {
            return back()->withErrors(['category_id' => 'Selecione uma categoria relacionada ao segmento escolhido.'])->withInput();
        }
        $belongs = Category::where('id', $data['category_id'])
            ->where('segment_id', $req->input('segment_id'))
            ->exists();
        if (! $belongs) {
            return back()->withErrors(['category_id' => 'A categoria selecionada não pertence ao segmento escolhido.'])->withInput();
        }

        return null;
    }
}
