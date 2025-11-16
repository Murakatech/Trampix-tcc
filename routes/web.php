<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyDashboardController;
use App\Http\Controllers\CompanyVacancyController;
use App\Http\Controllers\ConnectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\FreelancerDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\JobVacancyStatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// garante que {vaga} só aceite números
Route::pattern('vaga', '[0-9]+');

// Landing Page Principal
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Página pública de documentação sobre Categorias e Áreas de Atuação
Route::view('/docs/categorias-e-areas', 'docs.categories-activity-areas')->name('docs.categories_areas');

// Rotas de Dashboard Pós-Login
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Dashboard específico para freelancers
    Route::middleware(['can:isFreelancer'])->group(function () {
        Route::get('/freelancer/dashboard', [FreelancerDashboardController::class, 'index'])->name('freelancer.dashboard');
        Route::get('/freelancer/dashboard/updates', [FreelancerDashboardController::class, 'getUpdates'])->name('freelancer.dashboard.updates');
    });

    // Dashboard específico para empresas
    Route::middleware(['can:isCompany'])->group(function () {
        Route::get('/company/dashboard', [CompanyDashboardController::class, 'index'])->name('company.dashboard');
    });
});

// Conectar (UI básica)
Route::middleware(['auth'])->group(function () {
    Route::get('/connect', function () {
        $user = request()->user();
        $job = null;
        $companyJob = null;
        $candidate = null;
        $activeRole = session('active_role');
        $overrideRole = request()->query('role');
        if (in_array($overrideRole, ['freelancer','company'])) {
            session(['active_role' => $overrideRole]);
            if ($overrideRole === 'freelancer') {
                session()->forget('connect_company_job_id');
                session()->forget('connect_current_freelancer_id');
            } else {
                session()->forget('connect_current_job_id');
            }
            $activeRole = $overrideRole;
        }
        if (!$activeRole) {
            if ($user && method_exists($user, 'isFreelancer') && $user->isFreelancer() && $user->freelancer) {
                $activeRole = 'freelancer';
            } elseif ($user && method_exists($user, 'isCompany') && $user->isCompany() && $user->company) {
                $activeRole = 'company';
            }
        }

        if ($user && $activeRole === 'freelancer' && method_exists($user, 'isFreelancer') && $user->isFreelancer() && $user->freelancer) {
            $currentId = session('connect_current_job_id');
            $rejected = collect(session('connect_rejected', []))->map(fn($v)=> (int)$v)->all();
            $liked = collect(session('connect_liked', []))->map(fn($v)=> (int)$v)->all();
            $exclude = array_values(array_unique(array_merge($rejected, $liked)));
            if ($currentId) {
                $job = \App\Models\JobVacancy::query()->active()->with('company')->where('id', $currentId)->first();
            }
            if (!$job) {
                $query = \App\Models\JobVacancy::query()->active()->with('company');
                if (!empty($exclude)) {
                    $query->whereNotIn('id', $exclude);
                }
                $job = $query->inRandomOrder()->first();
                if ($job) {
                    session(['connect_current_job_id' => $job->id]);
                } else {
                    session()->forget('connect_current_job_id');
                }
            }
        } elseif ($user && $activeRole === 'company' && method_exists($user, 'isCompany') && $user->isCompany() && $user->company) {
            $company = $user->company;
            $selected = (int) request()->query('job_id', 0);
            $currentCompanyJobId = (int) (session('connect_company_job_id') ?? 0);
            if ($selected > 0) {
                $own = \App\Models\JobVacancy::query()->where('company_id', $company->id)->where('id', $selected)->first();
                if ($own) {
                    session(['connect_company_job_id' => $own->id]);
                    $currentCompanyJobId = $own->id;
                }
            }
            if ($currentCompanyJobId > 0) {
                $companyJob = \App\Models\JobVacancy::with('company')->find($currentCompanyJobId);
            }
            $currentFreelancerId = (int) (session('connect_current_freelancer_id') ?? 0);
            if ($currentFreelancerId > 0) {
                $candidate = \App\Models\Freelancer::with('user')->find($currentFreelancerId);
            }
            if (!$candidate) {
                $jobContextId = $currentCompanyJobId > 0 ? $currentCompanyJobId : 0;
                $rej = collect(session('connect_company_rejected_'.$jobContextId, []))->map(fn($v)=> (int)$v)->all();
                $likedF = collect(session('connect_company_liked_'.$jobContextId, []))->map(fn($v)=> (int)$v)->all();
                $dbLikedF = $jobContextId > 0
                    ? DB::table('connect_likes')
                        ->where('role', 'company')
                        ->where('job_vacancy_id', $jobContextId)
                        ->pluck('freelancer_id')
                        ->map(fn($v)=> (int)$v)
                        ->all()
                    : [];
                $matchedF = $jobContextId > 0
                    ? DB::table('connect_matches')
                        ->where('job_vacancy_id', $jobContextId)
                        ->pluck('freelancer_id')
                        ->map(fn($v)=> (int)$v)
                        ->all()
                    : [];
                $excludeF = array_values(array_unique(array_merge($rej, $likedF, $dbLikedF, $matchedF)));
                $fq = \App\Models\Freelancer::query()->where('is_active', true)->with('user');
                if (!empty($excludeF)) {
                    $fq->whereNotIn('id', $excludeF);
                }
                $candidate = $fq->inRandomOrder()->first();
                if ($candidate) {
                    session(['connect_current_freelancer_id' => $candidate->id]);
                } else {
                    session()->forget('connect_current_freelancer_id');
                }
            }
        }

        // Matches para o menu e novos matches para confete
        $userMatches = collect();
        $newMatches = [];
        $newMatchesCount = 0;
        $lastSeen = session('connect_last_seen');
        $now = now();
        if ($activeRole === 'freelancer' && $user && $user->freelancer) {
            $fid = $user->freelancer->id;
            $userMatches = DB::table('connect_matches')
                ->join('job_vacancies', 'connect_matches.job_vacancy_id', '=', 'job_vacancies.id')
                ->leftJoin('companies', 'job_vacancies.company_id', '=', 'companies.id')
                ->select('connect_matches.*', 'job_vacancies.title as job_title', 'job_vacancies.id as job_id', 'companies.display_name as company_name', 'companies.id as company_id', 'companies.profile_photo as company_logo')
                ->where('connect_matches.freelancer_id', $fid)
                ->orderByDesc('connect_matches.created_at')
                ->limit(20)
                ->get();
            if ($lastSeen) {
                $newRows = DB::table('connect_matches')
                    ->join('job_vacancies', 'connect_matches.job_vacancy_id', '=', 'job_vacancies.id')
                    ->leftJoin('companies', 'job_vacancies.company_id', '=', 'companies.id')
                    ->select('companies.display_name as company_name', 'job_vacancies.title as job_title')
                    ->where('connect_matches.freelancer_id', $fid)
                    ->where('connect_matches.created_at', '>', $lastSeen)
                    ->get();
                foreach ($newRows as $r) {
                    $newMatches[] = 'Você se conectou com '.$r->company_name.' na vaga "'.$r->job_title.'"';
                }
                $newMatchesCount = count($newRows);
            } else {
                // Primeira visita: mostrar como novos os matches existentes
                foreach ($userMatches as $r) {
                    $newMatches[] = 'Você se conectou com '.($r->company_name ?? 'Empresa').' na vaga "'.($r->job_title ?? 'Vaga').'"';
                }
                $newMatchesCount = count($userMatches);
            }
        } elseif ($activeRole === 'company' && $user && $user->company) {
            $cid = $user->company->id;
            $userMatches = DB::table('connect_matches')
                ->join('job_vacancies', 'connect_matches.job_vacancy_id', '=', 'job_vacancies.id')
                ->join('freelancers', 'connect_matches.freelancer_id', '=', 'freelancers.id')
                ->join('users', 'freelancers.user_id', '=', 'users.id')
                ->select('connect_matches.*', 'job_vacancies.title as job_title', 'users.name as freelancer_name', 'freelancers.id as freelancer_id', 'freelancers.profile_photo as freelancer_photo', 'users.id as user_id')
                ->where('job_vacancies.company_id', $cid)
                ->when(isset($currentCompanyJobId) && $currentCompanyJobId > 0, function($q) use ($currentCompanyJobId){
                    $q->where('job_vacancies.id', $currentCompanyJobId);
                })
                ->orderByDesc('connect_matches.created_at')
                ->limit(20)
                ->get();
            if ($lastSeen) {
                $newRows = DB::table('connect_matches')
                    ->join('job_vacancies', 'connect_matches.job_vacancy_id', '=', 'job_vacancies.id')
                    ->join('freelancers', 'connect_matches.freelancer_id', '=', 'freelancers.id')
                    ->join('users', 'freelancers.user_id', '=', 'users.id')
                    ->select('users.name as freelancer_name', 'job_vacancies.title as job_title')
                    ->where('job_vacancies.company_id', $cid)
                    ->when(isset($currentCompanyJobId) && $currentCompanyJobId > 0, function($q) use ($currentCompanyJobId){
                        $q->where('job_vacancies.id', $currentCompanyJobId);
                    })
                    ->where('connect_matches.created_at', '>', $lastSeen)
                    ->get();
                foreach ($newRows as $r) {
                    $newMatches[] = 'Você se conectou com '.$r->freelancer_name.' na vaga "'.$r->job_title.'"';
                }
                $newMatchesCount = count($newRows);
            } else {
                foreach ($userMatches as $r) {
                    $newMatches[] = 'Você se conectou com '.($r->freelancer_name ?? 'Freelancer').' na vaga "'.($r->job_title ?? 'Vaga').'"';
                }
                $newMatchesCount = count($userMatches);
            }
        }
        session(['connect_last_seen' => $now]);

        $confetti = (bool) session('connect_confetti', false) || ($newMatchesCount > 0);
        $confettiMessage = session('connect_confetti_message');
        if (session('connect_confetti')) { session()->forget('connect_confetti'); }
        if (session('connect_confetti_message')) { session()->forget('connect_confetti_message'); }

        return view('connect.index', [
            'job' => $job,
            'companyJob' => $companyJob,
            'candidate' => $candidate,
            'userMatches' => $userMatches,
            'confetti' => $confetti,
            'newMatches' => $newMatches,
            'newMatchesCount' => $newMatchesCount,
            'confettiMessage' => $confettiMessage,
        ]);
    })->name('connect.index');

    Route::post('/connect/decide', function () {
        $user = request()->user();
        if (!($user && method_exists($user, 'isFreelancer') && $user->isFreelancer() && $user->freelancer)) {
            return redirect()->route('dashboard');
        }
        $jobId = (int) request()->input('job_id');
        $action = request()->input('action');
        $currentId = (int) (session('connect_current_job_id') ?? 0);
        if ($jobId > 0 && $currentId === $jobId && in_array($action, ['liked', 'rejected'])) {
            if ($action === 'liked') {
                $liked = collect(session('connect_liked', []))->map(fn($v)=> (int)$v)->all();
                $liked[] = $jobId;
                session(['connect_liked' => array_values(array_unique($liked))]);

                // Persist like
                $freelancerId = $user->freelancer->id;
                try {
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
                } catch (\Throwable $e) {}

                // Reciprocal check
                $companyId = DB::table('job_vacancies')->where('id', $jobId)->value('company_id');
                $recip = DB::table('connect_likes')
                    ->where('role', 'company')
                    ->where('target_type', 'freelancer')
                    ->where('target_id', $freelancerId)
                    ->where('job_vacancy_id', $jobId)
                    ->exists();
                if ($recip) {
                    try {
                        $exists = DB::table('connect_matches')
                            ->where('freelancer_id', $freelancerId)
                            ->where('job_vacancy_id', $jobId)
                            ->exists();
                        if (! $exists) {
                            DB::table('connect_matches')->insert([
                                'freelancer_id' => $freelancerId,
                                'job_vacancy_id' => $jobId,
                                'created_at' => now(),
                            ]);
                        }
                        $companyName = DB::table('companies')->where('id', $companyId)->value('display_name');
                        $jobTitle = DB::table('job_vacancies')->where('id', $jobId)->value('title');
                        session([
                            'connect_confetti' => true,
                            'connect_confetti_message' => 'Você se conectou com '.($companyName ?: 'Empresa').' na vaga "'.($jobTitle ?: 'Vaga').'"',
                        ]);
                    } catch (\Throwable $e) {}
                }
            } else {
                $rejected = collect(session('connect_rejected', []))->map(fn($v)=> (int)$v)->all();
                $rejected[] = $jobId;
                session(['connect_rejected' => array_values(array_unique($rejected))]);
            }
            session()->forget('connect_current_job_id');
        }
        return redirect()->route('connect.index');
    })->name('connect.decide');

    Route::post('/connect/company/decide', function () {
        $user = request()->user();
        if (!($user && method_exists($user, 'isCompany') && $user->isCompany() && $user->company)) {
            return redirect()->route('dashboard');
        }
        $freelancerId = (int) request()->input('freelancer_id');
        $action = request()->input('action');
        $currentF = (int) (session('connect_current_freelancer_id') ?? 0);
        if ($freelancerId > 0 && $currentF === $freelancerId && in_array($action, ['liked', 'rejected'])) {
            if ($action === 'liked') {
                $jobId = (int) (session('connect_company_job_id') ?? 0);
                $likedKey = 'connect_company_liked_'.$jobId;
                $liked = collect(session($likedKey, []))->map(fn($v)=> (int)$v)->all();
                $liked[] = $freelancerId;
                session([$likedKey => array_values(array_unique($liked))]);

                // Persist like
                try {
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
                } catch (\Throwable $e) {}

                // Reciprocal check
                if ($jobId > 0) {
                    $recip = DB::table('connect_likes')
                        ->where('role', 'freelancer')
                        ->where('target_type', 'job')
                        ->where('target_id', $jobId)
                        ->where('freelancer_id', $freelancerId)
                        ->exists();
                    if ($recip) {
                        try {
                            $exists = DB::table('connect_matches')
                                ->where('freelancer_id', $freelancerId)
                                ->where('job_vacancy_id', $jobId)
                                ->exists();
                            if (! $exists) {
                                DB::table('connect_matches')->insert([
                                    'freelancer_id' => $freelancerId,
                                    'job_vacancy_id' => $jobId,
                                    'created_at' => now(),
                                ]);
                            }
                            $freelancerName = DB::table('users')
                                ->join('freelancers', 'users.id', '=', 'freelancers.user_id')
                                ->where('freelancers.id', $freelancerId)
                                ->value('users.name');
                            session([
                                'connect_confetti' => true,
                                'connect_confetti_message' => 'Você se conectou com '.($freelancerName ?: 'Freelancer'),
                            ]);
                        } catch (\Throwable $e) {}
                    }
                }
            } else {
                $jobId = (int) (session('connect_company_job_id') ?? 0);
                $rejKey = 'connect_company_rejected_'.$jobId;
                $rejected = collect(session($rejKey, []))->map(fn($v)=> (int)$v)->all();
                $rejected[] = $freelancerId;
                session([$rejKey => array_values(array_unique($rejected))]);
            }
            session()->forget('connect_current_freelancer_id');
        }
        return redirect()->route('connect.index');
    })->name('connect.company.decide');

    Route::post('/connect/reset', function () {
        $user = request()->user();
        session()->forget('connect_liked');
        session()->forget('connect_rejected');
        session()->forget('connect_current_job_id');
        session()->forget('connect_company_liked');
        session()->forget('connect_company_rejected');
        session()->forget('connect_current_freelancer_id');
        if ($user && method_exists($user, 'isCompany') && $user->isCompany() && $user->company) {
            return redirect()->route('connect.index', ['role' => 'company']);
        }
        if ($user && method_exists($user, 'isFreelancer') && $user->isFreelancer() && $user->freelancer) {
            return redirect()->route('connect.index', ['role' => 'freelancer']);
        }
        return redirect()->route('connect.index');
    })->name('connect.reset');
});

// Seleção de vaga antes do matchmaking (empresa)
Route::middleware(['auth', 'can:isCompany'])->group(function () {
    Route::get('/connect/jobs', function () {
        $user = request()->user();
        $company = $user->company;
        $companyVacancies = $company ? $company->vacancies()->active()->with(['company.segment','category.segment'])->latest()->get() : collect();
        $grouped = $companyVacancies->groupBy(function ($job) {
            $segName = null;
            if ($job->category && $job->category->segment) {
                $segName = $job->category->segment->name;
            } elseif ($job->company && $job->company->segment) {
                $segName = $job->company->segment->name;
            }
            return $segName ?: 'Outros';
        });
        return view('connect.jobs', ['grouped' => $grouped]);
    })->name('connect.jobs');
});

// Matchmaking dedicado removido em favor de /connect

// Protegido (auth + empresa) — registre ANTES
Route::middleware(['auth', 'can:isCompany'])->group(function () {
    Route::resource('vagas', \App\Http\Controllers\JobVacancyController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    // Rota adicional para job-vacancies.create (compatibilidade com dashboard)
    Route::get('/job-vacancies/create', [JobVacancyController::class, 'create'])
        ->name('job-vacancies.create');

    // Rotas específicas para vagas da empresa
    Route::get('/company/vagas', [CompanyVacancyController::class, 'index'])->name('company.vagas.index');
    Route::get('/company/vagas/{vaga}', [CompanyVacancyController::class, 'show'])->name('company.vagas.show');
    Route::patch('/company/vagas/{vaga}/toggle-status', [CompanyVacancyController::class, 'toggleStatus'])->name('company.vagas.toggle-status');

    Route::get('/job-vacancies/{id}/applications', [ApplicationController::class, 'byVacancy'])
        ->name('applications.byVacancy');

    Route::get('/applications/manage', [ApplicationController::class, 'manage'])
        ->name('applications.manage');

    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])
        ->name('applications.updateStatus');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Removidas rotas de pages de demonstração e styleguide

// Público
Route::resource('vagas', JobVacancyController::class)->only(['index', 'show']);
// API pública para sugestões de busca de vagas
Route::get('/api/vagas/suggest', [JobVacancyController::class, 'suggest'])->name('api.vagas.suggest');
// API pública: categorias por segmento (para filtro dinâmico)
Route::get('/api/segments/{segment}/categories', [JobVacancyController::class, 'categoriesBySegment'])
    ->name('api.segments.categories');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
// Perfil público unificado (visualização)
Route::get('/profiles/{user}', [ProfileController::class, 'show'])->name('profiles.show');

// Perfil / dashboard
Route::middleware('auth')->group(function () {
    // Tela de seleção de perfil e troca
    \App\Http\Controllers\Auth\RoleSelectionController::class; // ensure class reference exists for static analysis
    Route::get('/select-role', [\App\Http\Controllers\Auth\RoleSelectionController::class, 'show'])->name('select-role.show');
    Route::post('/select-role', [\App\Http\Controllers\Auth\RoleSelectionController::class, 'select'])->name('select-role.select');
    Route::post('/switch-role', [\App\Http\Controllers\Auth\RoleSelectionController::class, 'switch'])->name('select-role.switch');
    // Nova rota para configurações de conta
    Route::get('/profile/account', [ProfileController::class, 'account'])->name('profile.account');
    Route::patch('/profile/account', [ProfileController::class, 'updateAccount'])->name('profile.account.update');

    // Rota principal do perfil (baseada no active_role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota para trocar perfil
    Route::post('/profile/switch-role', [ProfileController::class, 'switchRole'])->name('profile.switch-role');

    // Rota para obter dados do perfil freelancer em JSON
    Route::get('/profile/freelancer', [ProfileController::class, 'showFreelancerProfile'])->name('profile.freelancer.show');

    // Rotas para exclusão de perfis específicos
    Route::delete('/profile/freelancer', [ProfileController::class, 'destroyFreelancerProfile'])->name('profile.freelancer.destroy');
    Route::delete('/profile/company', [ProfileController::class, 'destroyCompanyProfile'])->name('profile.company.destroy');

    // Rotas para upload de foto de perfil
    Route::post('/profile/photo/upload', [ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo/delete', [ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');

    // API para atualização dinâmica de perfil
    Route::get('/api/profile/check-updates', [ProfilePhotoController::class, 'checkUpdates'])->name('api.profile.check-updates');
    Route::get('/api/profile/data', [ProfilePhotoController::class, 'getProfileData'])->name('api.profile.data');

    // Rotas para imagens de perfil (freelancer e empresa)
    Route::patch('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.image.update');
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');

    // Rotas para upload de currículo
    Route::post('/profile/cv/upload', [ProfileController::class, 'uploadCv'])->name('profile.cv.upload');
    Route::delete('/profile/cv/delete', [ProfileController::class, 'deleteCv'])->name('profile.cv.delete');

    // Rotas para perfis de freelancer
    Route::get('/freelancers/profile', [FreelancerController::class, 'showOwn'])->name('freelancers.profile');
    Route::resource('freelancers', FreelancerController::class)
        ->only(['show', 'create', 'store', 'update', 'destroy']);
    // Redirect legado para edição de freelancer → /profile
    Route::get('/freelancers/{freelancer}/edit', function () {
        return redirect()->route('profile.edit');
    })->name('freelancers.edit');
    Route::get('/freelancers/{freelancer}/download-cv', [FreelancerController::class, 'downloadCv'])
        ->name('freelancers.download-cv');

    // Rotas para perfis de empresa (autenticadas)
    Route::resource('companies', CompanyController::class)
        ->only(['create', 'store', 'update', 'destroy']);
    // Redirect legado para edição de empresa → /profile
    Route::get('/companies/{company}/edit', function () {
        return redirect()->route('profile.edit');
    })->name('companies.edit');
    // Removido: rota legada de vagas por empresa (/companies/{company}/vacancies)
});

Route::middleware(['auth'])->group(function () {
    // Página de Status da Vaga (empresa dona ou freelancer aceito)
    Route::get('/vagas/{vaga}/status', [JobVacancyStatusController::class, 'show'])
        ->name('vagas.status');
    // Avaliações pós-contrato (empresa ou freelancer)
    Route::get('/applications/{application}/evaluate', [EvaluationController::class, 'create'])
        ->name('applications.evaluate.create');
    Route::post('/applications/{application}/evaluate', [EvaluationController::class, 'store'])
        ->name('applications.evaluate.store');
    Route::get('/applications/{application}/evaluation', [EvaluationController::class, 'show'])
        ->name('applications.evaluate.show');
    Route::post('/job-vacancies/{id}/apply', [ApplicationController::class, 'store'])
        ->name('applications.store')
        ->middleware('can:isFreelancer');

    Route::get('/my-applications', [ApplicationController::class, 'index'])
        ->name('applications.index');
    // Trabalhos finalizados (empresa ou freelancer, despacha para a view correta)
    Route::get('/finished-jobs', [ApplicationController::class, 'finishedIndex'])
        ->name('finished.index');

    Route::delete('/applications/{application}', [ApplicationController::class, 'cancel'])
        ->name('applications.cancel')
        ->middleware('can:isFreelancer');
    // Reconhecer todas as rejeições (freelancer) para ocultar aviso
    Route::post('/applications/ack-rejections', [ApplicationController::class, 'acknowledgeAllRejections'])
        ->name('applications.ack.all')
        ->middleware('can:isFreelancer');

    // Freelancer se demite de uma parceria ativa
    Route::patch('/applications/{application}/resign', [ApplicationController::class, 'resign'])
        ->name('applications.resign')
        ->middleware('can:isFreelancer');
});

// Rotas administrativas
Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::get('/admin/freelancers', [FreelancerController::class, 'index'])->name('admin.freelancers');
    Route::get('/admin/companies', [CompanyController::class, 'index'])->name('admin.companies');
    Route::get('/admin/applications', [ApplicationController::class, 'adminIndex'])->name('admin.applications');
    // Rota específica para admins criarem vagas
    Route::get('/admin/vagas/create', [JobVacancyController::class, 'create'])
        ->name('admin.vagas.create');
    // Rota POST para admins salvarem vagas
    Route::post('/admin/vagas', [JobVacancyController::class, 'store'])
        ->name('admin.vagas.store');

    // Administração de Categorias
    Route::get('/admin/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])
        ->name('admin.categories.index');
    Route::post('/admin/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])
        ->name('admin.categories.store');
    Route::patch('/admin/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])
        ->name('admin.categories.update');
    // Ativação/Desativação de categorias
    Route::patch('/admin/categories/{category}/deactivate', [\App\Http\Controllers\Admin\CategoryController::class, 'deactivate'])
        ->name('admin.categories.deactivate');
    Route::patch('/admin/categories/{category}/reactivate', [\App\Http\Controllers\Admin\CategoryController::class, 'reactivate'])
        ->name('admin.categories.reactivate');

    // Administração de Segmentos (gerenciados na mesma tela de categorias)
    Route::post('/admin/segments', [\App\Http\Controllers\Admin\SegmentController::class, 'store'])
        ->name('admin.segments.store');
    Route::patch('/admin/segments/{segment}', [\App\Http\Controllers\Admin\SegmentController::class, 'update'])
        ->name('admin.segments.update');
    // Ativação/Desativação de segmentos
    Route::patch('/admin/segments/{segment}/deactivate', [\App\Http\Controllers\Admin\SegmentController::class, 'deactivate'])
        ->name('admin.segments.deactivate');
    Route::patch('/admin/segments/{segment}/reactivate', [\App\Http\Controllers\Admin\SegmentController::class, 'reactivate'])
        ->name('admin.segments.reactivate');
});

require __DIR__.'/auth.php';
