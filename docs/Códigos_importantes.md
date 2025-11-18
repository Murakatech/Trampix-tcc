# Códigos importantes: CRUD de Vagas e Matchmaking

## CRUD de Vagas

### Rotas

```
// Protegido (empresa): create/store/edit/update/destroy
routes/web.php:419-421
Route::middleware(['auth', 'can:isCompany'])->group(function () {
    Route::resource('vagas', \App\Http\Controllers\JobVacancyController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

// Público: index/show e API de sugestões
routes/web.php:448-453
Route::resource('vagas', JobVacancyController::class)->only(['index', 'show']);
Route::get('/api/vagas/suggest', [JobVacancyController::class, 'suggest'])->name('api.vagas.suggest');
Route::get('/api/segments/{segment}/categories', [JobVacancyController::class, 'categoriesBySegment'])
    ->name('api.segments.categories');
```

### Model e Scopes

```
// Campos e relações
app/Models/JobVacancy.php:13-25,27-45
protected $fillable = ['company_id','title','description','requirements','category_id','service_category_id','location_type','salary_range','salary_min','salary_max','status'];
public function company() { return $this->belongsTo(Company::class); }
public function applications() { return $this->hasMany(Application::class); }
public function category() { return $this->belongsTo(\App\Models\Category::class); }

// Scopes usados nos filtros da listagem
app/Models/JobVacancy.php:50-123
public function scopePublicList($q) { return $q->active()->whereDoesntHave('applications', fn($a)=> $a->whereIn('status',['accepted','ended'])); }
public function scopeNotAppliedBy($q,$fid) { /* oculta vagas já aplicadas pelo freelancer */ }
public function scopeFilterSegment($q,$segmentId) { /* filtra categorias do segmento */ }
public function scopeLocationType($q,$type) { /* Remoto/Híbrido/Presencial */ }
public function scopeSearch($q,$search) { /* title/description/requirements + company.name */ }
```

### Listagem e Filtros (index)

```
app/Http/Controllers/JobVacancyController.php:19-126
$query = JobVacancy::with(['company:id,name,user_id,segment_id','company.segment:id,name','applications:id,job_vacancy_id,freelancer_id','category:id,name,segment_id','category.segment:id,name'])
    ->select(['id','title','description','requirements','category_id','location_type','salary_range','status','company_id','created_at'])
    ->publicList()
    ->orderBy('created_at','desc');

// Oculta vagas já aplicadas pelo freelancer
if (Auth::check() && Gate::allows('isFreelancer')) { $query->notAppliedBy($freelancerId); }

// Filtros: categoria, segmento, modalidade, busca
$query->locationType($request->location_type);
$query->search($request->search);

// Ordenação por média pública de avaliação da empresa (subquery AVG)
app/Http/Controllers/JobVacancyController.php:84-106
```

### Criação (create/store)

```
// Formulário
resources/views/vagas/create.blade.php:11
<form action="{{ Gate::allows('isAdmin') ? route('admin.vagas.store') : route('vagas.store') }}" method="POST">

// Controller
app/Http/Controllers/JobVacancyController.php:149-200
public function store(StoreJobVacancyRequest $req) {
  Gate::authorize('isCompany') || Gate::authorize('isAdmin');
  $validated = $req->validated();
  $company = Gate::allows('isAdmin') && $req->filled('company_id')
    ? Company::findOrFail($req->company_id)
    : Company::firstOrCreate(['user_id'=>Auth::id()], ['name'=>Auth::user()->name]);
  $data = $validated; $data['company_id'] = $company->id;
  $this->resolveCategoryIdFromLegacy($req, $data);
  $invalid = $this->validateCategoryBelongsToSegment($req, $data); if ($invalid) return $invalid;
  $data['status'] = $data['status'] ?? 'active';
  $vaga = JobVacancy::create($data);
  return redirect()->route('vagas.show',$vaga)->with('ok','Vaga criada.');
}

// Validação
app/Http/Requests/StoreJobVacancyRequest.php:17-28
```

### Edição/Atualização (edit/update)

```
// Formulário
resources/views/vagas/edit.blade.php:15-18,47-71,77-116

// Controller
app/Http/Controllers/JobVacancyController.php:237-273
public function update(UpdateJobVacancyRequest $req, JobVacancy $vaga) {
  Gate::authorize('isCompany'); assert($vaga->company?->user_id === Auth::id());
  $data = $req->validated();
  $this->resolveCategoryIdFromLegacy($req, $data);
  $invalid = $this->validateCategoryBelongsToSegment($req, $data); if ($invalid) return $invalid;
  $vaga->update($data);
  return redirect()->route('vagas.show',$vaga)->with('ok','Vaga atualizada.');
}

// Validação
app/Http/Requests/UpdateJobVacancyRequest.php:17-28
```

### Visualização (show)

```
app/Http/Controllers/JobVacancyController.php:203-234
// Cache da vaga + relações e flag se freelancer já aplicou
return view('vagas.show', ['vaga' => $vagaWithRelations, 'hasApplied' => $hasApplied]);
```

### Remoção (destroy)

``@
app/Http/Controllers/JobVacancyController.php:275-290
public function destroy(JobVacancy $vaga) {
  Gate::authorize('isCompany'); assert($vaga->company?->user_id === Auth::id());
  $vaga->delete();
  return redirect()->route('vagas.index')->with('ok','Vaga removida.');
}
```

---

## Matchmaking (/connect)

### Fluxo Geral

- Freelancer entra em `/connect` e recebe uma vaga ativa não curtida/rejeitada; Empresa escolhe uma vaga em `/connect/jobs` e recebe candidatos.
- Ações “Curtir”/“Rejeitar” persistem em `connect_likes`; “matches” são criados em `connect_matches` quando há reciprocidade.

### Rotas e Session

```
// Tela principal e lógica
routes/web.php:19-135 (contexto), 145-234 (monta lista de matches + confetes)
// Decisão do freelancer
routes/web.php:236-303 (persist like e checa reciprocidade)
// Decisão da empresa
routes/web.php:304-376 (persist like e checa reciprocidade)
// Reset da sessão
routes/web.php:378-394
// Seleção de vaga (empresa)
routes/web.php:396-413
```

### Persistência de Likes e Criação de Match (Exemplos reais)

```
// Freelancer curte uma vaga
routes/web.php:250-263,266-293
DB::table('connect_likes')->insert([
  'liker_user_id' => $user->id,
  'role' => 'freelancer',
  'target_type' => 'job',
  'target_id' => $jobId,
  'job_vacancy_id' => $jobId,
  'freelancer_id' => $freelancerId,
  'company_id' => DB::table('job_vacancies')->where('id', $jobId)->value('company_id'),
  'created_at' => now(),
]);

// Checa reciprocidade (empresa curtiu freelancer nessa vaga)
$recip = DB::table('connect_likes')
  ->where('role','company')->where('target_type','freelancer')
  ->where('target_id',$freelancerId)->where('job_vacancy_id',$jobId)->exists();
if ($recip && ! DB::table('connect_matches')->where('freelancer_id',$freelancerId)->where('job_vacancy_id',$jobId)->exists()) {
  DB::table('connect_matches')->insert(['freelancer_id'=>$freelancerId,'job_vacancy_id'=>$jobId,'created_at'=>now()]);
}
```

```
// Empresa curte um freelancer
routes/web.php:320-333,335-363
DB::table('connect_likes')->insert([
  'liker_user_id' => $user->id,
  'role' => 'company',
  'target_type' => 'freelancer',
  'target_id' => $freelancerId,
  'job_vacancy_id' => $jobId ?: null,
  'freelancer_id' => $freelancerId,
  'company_id' => $user->company->id,
  'created_at' => now(),
]);

// Reciprocidade: freelancer curtiu a vaga
$recip = DB::table('connect_likes')
  ->where('role','freelancer')->where('target_type','job')
  ->where('target_id',$jobId)->where('freelancer_id',$freelancerId)->exists();
if ($recip && ! DB::table('connect_matches')->where('freelancer_id',$freelancerId)->where('job_vacancy_id',$jobId)->exists()) {
  DB::table('connect_matches')->insert(['freelancer_id'=>$freelancerId,'job_vacancy_id'=>$jobId,'created_at'=>now()]);
}
```

### Exibição na Interface

```
// Freelancer vendo uma vaga e curtindo
resources/views/connect/index.blade.php:17-22,60-66
<form method="POST" action="{{ route('connect.decide') }}"> <!-- rejected/liked -->

// Empresa vendo candidato e curtindo
resources/views/connect/index.blade.php:81-86,120-125
<form method="POST" action="{{ route('connect.company.decide') }}"> <!-- rejected/liked -->

// Menu de matches
resources/views/connect/index.blade.php:159-199
```

### Listagem de Matches

```
// Freelancer: junta matches com vaga e empresa
routes/web.php:151-160
DB::table('connect_matches')
  ->join('job_vacancies', 'connect_matches.job_vacancy_id','=','job_vacancies.id')
  ->leftJoin('companies','job_vacancies.company_id','=','companies.id')
  ->select('connect_matches.*','job_vacancies.title as job_title','companies.display_name as company_name','companies.id as company_id','companies.profile_photo as company_logo')
  ->where('connect_matches.freelancer_id',$fid)
  ->orderByDesc('connect_matches.created_at')
  ->limit(20)
  ->get();

// Empresa: junta matches com vaga e freelancer+user
routes/web.php:182-193
```

## Observações

- O CRUD valida segmento/categoria consistentemente e usa caches para otimizar listagem e visualização.
- O matchmaking persiste preferências em `connect_likes` e só cria `connect_matches` quando ambos os lados curtem.
- Há tratamento de sessão para controlar itens já vistos e animar confetes em novos matches.